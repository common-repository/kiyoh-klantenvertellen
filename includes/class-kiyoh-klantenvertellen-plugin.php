<?php
/**
 * Main plugin class
 *
 * @version 2.0.0
 * @package KiyOh_Klantenvertellen
 * @author Bart Pluijms
 */

namespace KiyOh_Klantenvertellen;

class KiyOh_Klantenvertellen_Plugin {

    /** @var Settings_Page $settings_page */

    private $settings_page;

    /** @var array Provider urls */

    public $provider_urls = array(
        'kiyoh' => array(
            'xml' => 'https://www.kiyoh.nl/xml/recent_company_reviews.xml',
			'api'=> 'https://www.kiyoh.com/v1/publication/review/external'
        ),
        'kiyoh_com' => array(
            'xml' => 'https://www.kiyoh.com/xml/recent_company_reviews.xml',
			'api'=> 'https://www.kiyoh.com/v1/publication/review/external'
        ),
        'klantenvertellen' => array(
            'xml' => 'https://www.klantenvertellen.nl/xml',
            'default' => 'https://www.klantenvertellen.nl/referenties',
			'api'=> 'https://www.klantenvertellen.nl/v1/publication/review/external'
        ),
        'klantenvertellen_mobiliteit' => array(
            'xml' => 'https://mobiliteit.klantenvertellen.nl/xml',
        ),
        'klantenvertellen_v2' => array(
            'xml' => 'https://www.klantenvertellen.nl/v1/review/feed.xml',
        ),
    );
	
	public static $plugin_settings_id = 'kk_plugin_settings';

    public static $layouts = array('default', 'slider', 'list');

    /** @var array $settings_ids Setting IDs, used to save settings from different tabs */
    public $settings_ids = array();

    /* @var array Current plugin settings */
    public $plugin_settings;
    public function __construct()
    {
		
        // Set the settings ids
        $this->settings_ids = array(
            'general' => static::$plugin_settings_id,
            'default_settings' => static::$plugin_settings_id.'_default_settings',
			'single_review_settings' => static::$plugin_settings_id.'_single_review_settings',
            'emails' => static::$plugin_settings_id.'_emails',
        );

        if ($this->plugin_settings === null) {

            foreach ($this->settings_ids AS $setting_key => $setting_value) {
                $this->plugin_settings[$setting_key] = get_option($setting_value);
            }

        }

        $this->translation_load_textdomain();

		if(is_admin()) {
			add_filter('plugin_action_links_'.KK_PLUGIN_FILE, array($this, 'plugin_links'));
		}
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );
        add_action( 'wp_enqueue_scripts', array($this, 'register_slider_scripts') );
        add_action( 'admin_enqueue_scripts', array($this, 'register_slider_scripts') );
        add_action( 'widgets_init', array($this, 'register_widgets') );
		
		add_action('admin_init',array($this,'create_old_xml_backup'));

        add_filter('kk_plugin_single_review_text', array($this, 'limit_single_review_length'), 10, 3);
		//add_filter('kk_plugin_single_review_title', array($this, 'format_single_review_title'), 10, 3);
		add_filter('kk_plugin_single_review_recommendation', array($this, 'format_single_review_recommendation'), 10, 4);
		
		add_action( 'in_plugin_update_message-kiyoh-klantenvertellen/mso-kiyoh-klantenvertellen.php', array($this,'plugin_update_message'), 10, 2 );
		
        // WooCommerce
        if (self::is_woocommerce_activated()) {
            add_action( 'woocommerce_order_status_completed', array($this, 'maybe_send_invite_email'), 10, 1 );
            add_action( 'cron_send_order_invite_emails',  array($this, 'cron_send_order_invite_emails') );
            add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array($this, 'custom_order_query_var'), 10, 2 );
        }
		
		add_action( 'admin_notices', array($this,'api_admin_notice') );

    }

    /**
     * Enqueue scripts
     *
     * @return void
     */

    public function enqueue_scripts()
    {

        if (apply_filters('kk_plugin_enable_css', true) == true) {
            wp_register_style( KK_PLUGIN_ID.'_frontend', KK_PLUGIN_DIR_URL.'assets/css/frontend.css' );
	        wp_enqueue_style( KK_PLUGIN_ID.'_frontend' );
        }

        wp_register_style( KK_PLUGIN_ID.'_slider_style', KK_PLUGIN_DIR_URL.'assets/lightslider/css/lightslider.css' );
        wp_register_script( KK_PLUGIN_ID.'_slider_script', KK_PLUGIN_DIR_URL.'assets/lightslider/js/lightslider.js', array('jquery') );
    }

    /**
     * Register slider scripts
     *
     * @return void
     */

    public function register_slider_scripts()
    {
		wp_register_style( KK_PLUGIN_ID.'_slider_style', KK_PLUGIN_DIR_URL.'assets/lightslider/css/lightslider.css' );
        wp_register_script( KK_PLUGIN_ID.'_slider_script', KK_PLUGIN_DIR_URL.'assets/lightslider/js/lightslider.js', array('jquery') );
    }
	
    /**
     * Translation text domain
     */

    public function translation_load_textdomain()
    {
        load_plugin_textdomain('kk_plugin', false, dirname( KK_PLUGIN_FILE ) . '/langs/');
    }


    /**
     * Register widgets
     *
     * @return void
     */

    public function register_widgets()
    {

        $widget = new Widget($this);
        register_widget( $widget );

    }

    /**
     * Get a (widget) layout
     *
     * @param string $layout Type of layout to render
     * @param array $options Optional options to pass to the layout
     * @return string Layout (HTML)
     */

    public function get_layout( $layout = 'default', $options )
    {

        if (!in_array($layout, self::$layouts)) {
            return __('Layout not available, please use', 'kk_plugin').': '.implode(',', self::$layouts);
        }

        if (!isset($options['show_reviews_rating_higher_than']) || empty($options['show_reviews_rating_higher_than'])) {
            $options['show_reviews_rating_higher_than'] = apply_filters('kk_show_reviews_rating_higher_than', 0);
        }

        $provider = $this->plugin_settings['general']['kk_provider'];

        $data = $this->get_data($provider);
		
        if (!$data)
            return sprintf(__('No data found, please check your %s settings', 'kk_plugin'), self::get_name($provider));

        if ($layout == 'slider') {
            wp_enqueue_script(KK_PLUGIN_ID.'_slider_script');
            wp_enqueue_style(KK_PLUGIN_ID.'_slider_style');
            $options['random_id'] = wp_generate_password(12, false, false); // Needed for slider in case of multiple widgets / shortcodes
			
			$script = 'jQuery(document).ready(function($) {
				$("#slider-' . $options['random_id'] . ' .kk-reviews").lightSlider({
					item: 1,
					controls: false,
					adaptiveHeight: true,' 
					.(isset($options['auto_slide']) && $options['auto_slide'] == 'yes' ? '
					auto: true,
					pause: '.apply_filters('kk_plugin_slider_auto_delay', 6000).',
					loop: true' : false).'
				});
			});';
			
			wp_add_inline_script( KK_PLUGIN_ID.'_slider_script', $script, 'after' );
        }

        $options['layout'] = $layout; // So we know anywhere in the template files which layout is used
		
		$options['show_id']=(isset($this->plugin_settings['default_settings']['debugid']))?$this->plugin_settings['default_settings']['debugid']:0;

        return $this->get_template_content($layout, array('data' => $data, 'provider' => $provider, 'options' => $options));

    }

    /**
     * Puts output in buffering and return contents
     *
     * @param string $template_name The template filename to be used
     * @param array Variables passed to the template file
     * @return string Template contents
     */

     private function get_template_content($template_name = 'default', $params = array())
     {

       global $post;

       extract($params); // Array is now available as variable (array key = variable)

       ob_start();
	   include(self::get_template( $template_name.'.php'));
       //include(KK_PLUGIN_VIEWS_PATH.'/'.$template_name.'.php');
       $return = ob_get_contents();
       ob_end_clean();
       return $return;

    }

    /**
     * Get KiyOh data
     *
     * @return void
     */

     public function get_kiyoh_data()
     {

        if (isset( $this->plugin_settings['general']['kiyoh_connectorcode'] ))
            $connectorcode = $this->plugin_settings['general']['kiyoh_connectorcode'];

        if (isset($this->plugin_settings['general']['kiyoh_company_id']))
            $company_id = $this->plugin_settings['general']['kiyoh_company_id'];

         if (!empty($connectorcode) && !empty($company_id)) {

             $request_url = $this->provider_urls['kiyoh']['xml'].'?connectorcode=' . $connectorcode . '&company_id=' . $company_id;

             $request = $this->do_request($request_url);

             if (isset($request->error)) {
                 return false;
             } else {
                 return $request;
             }

         } else {

             // TODO Error handling

         }

     }

     /**
      * Get KiyOh data
      *
      * @return void
      */

      public function get_kiyoh_com_data()
      {

         if (isset( $this->plugin_settings['general']['kiyoh_com_connectorcode'] ))
             $connectorcode = $this->plugin_settings['general']['kiyoh_com_connectorcode'];

         if (isset($this->plugin_settings['general']['kiyoh_com_company_id']))
             $company_id = $this->plugin_settings['general']['kiyoh_com_company_id'];

          if (!empty($connectorcode) && !empty($company_id)) {

              $request_url = $this->provider_urls['kiyoh_com']['xml'].'?connectorcode=' . $connectorcode . '&company_id=' . $company_id;

              $request = $this->do_request($request_url);

              if (isset($request->error)) {
                  return false;
              } else {
                  return $request;
              }

          } else {

              // TODO Error handling

          }

      }

     /**
      * Check if data can be retrieved
      *
      * @param string $provider Provider
      * @return boolean
      */
     public function check_data_connection($provider)
     {
	 
		if($provider!=$this->plugin_settings['general']['kk_provider']) return false;
		
     	if ( false === ( $connection_check = get_transient( 'kk_plugin_connection_'.$provider) ) ) {
			
			if ($provider == 'kiyoh') {
				$data = $this->get_kiyoh_data();
			} elseif ($provider == 'kiyoh_com') {
				$data = $this->get_kiyoh_com_data();
			} elseif ($provider == 'klantenvertellen') {
				$data = $this->get_klantenvertellen_data();
			} elseif ($provider == 'klantenvertellen_mobiliteit') {
				$data = $this->get_klantenvertellen_mobiliteit_data();
			} elseif ($provider == 'klantenvertellen_v2') {
				$data = $this->get_klantenvertellen_v2_data();
			}elseif ($provider == 'klantenvertellen_api') {
				$data = $this->get_klantenvertellen_api_data();
			}elseif ($provider == 'kiyoh_api') {
				$data = $this->get_kiyoh_api_data();
			}
			
			if(isset($data->httpCode) && $data->httpCode==500) return false;

			if ($data==true) {

				set_transient( 'kk_plugin_connection_'.$provider, true, 24 * HOUR_IN_SECONDS);
				return true;
			}

			return false;

		} 

		else {

			return true;
		}

     }

     /**
      * Get Klantenvertellen data
      *
      * @return string|false Data or false
      */

      public function get_klantenvertellen_data()
      {

          if (isset($this->plugin_settings['general']['klantenvertellen_slug']))
            $slug = $this->plugin_settings['general']['klantenvertellen_slug'];

          if (!empty($slug)) {

              $request_url = $this->provider_urls['klantenvertellen']['xml'].'/'.$slug.'/all';

              $request = $this->do_request($request_url);

              if (isset($request->statistieken)) {
                  return $request;
              } else {
                  return false;
              }

          } else {

              return false;

          }

      }

      /**
       * Get Klantenvertellen V2 data
       *
       * @since 1.0.2
       * @return string|false Data or false
       */

       public function get_klantenvertellen_v2_data()
       {
           /* Deprecated since 1.1.4
		   if (isset($this->plugin_settings['general']['klantenvertellen_v2_tenant_id']) && is_numeric($this->plugin_settings['general']['klantenvertellen_v2_tenant_id']))
                $tenant_id = $this->plugin_settings['general']['klantenvertellen_v2_tenant_id'];

           if (isset($this->plugin_settings['general']['klantenvertellen_v2_location_id']))
                $location_id = $this->plugin_settings['general']['klantenvertellen_v2_location_id'];
		   */
		   if (isset($this->plugin_settings['general']['klantenvertellen_v2_hashxml']))
                $xmlfeed = $this->plugin_settings['general']['klantenvertellen_v2_hashxml'];
           
		   if (!empty($xmlfeed)) {
			   
			   $hash=str_replace($this->provider_urls['klantenvertellen_v2']['xml'].'?hash=','',$xmlfeed);
			  
               $request_url = $this->provider_urls['klantenvertellen_v2']['xml'].'?hash='.$hash;

               $request = $this->do_request($request_url);

               if (isset($request->reviews)) {
                   return $request;
               } else {
                   return false;
               }

           } else {
               return false;
           }

       }

       /**
        * Get Klantenvertellen (mobiliteit) data
        *
        * @since 1.0.2
        * @return string|false Data or false
        */

        public function get_klantenvertellen_mobiliteit_data()
        {

            if (isset($this->plugin_settings['general']['klantenvertellen_mobiliteit_slug']))
              $slug = $this->plugin_settings['general']['klantenvertellen_mobiliteit_slug'];

            if (!empty($slug)) {

                $request_url = $this->provider_urls['klantenvertellen_mobiliteit']['xml'].'/'.$slug.'/all';

                $request = $this->do_request($request_url);

                if (isset($request->statistieken)) {
                    return $request;
                } else {
                    return false;
                }

            } else {

                return false;

            }

        }

      /**
       * Map the KiyOh and Klantenvertellen XML to a uniform array
       *
       * @param string $provider Either 'kiyoh' or 'klantenvertellen'
       *
       * @return array|false Array of data or false if no data is available
       */

      public function get_data($provider)
      {

          $mapped_data = array();
		  
          $transient_name = 'kk_plugin_'.$provider;
		  
		  if( $this->get_debug_status() ) {
			self::clear_transients();
		  }
		  
        if (empty(get_transient('kk_plugin_last_check'))) {
			
          if (!empty(get_transient('kk_plugin_connection_down'))) {
            $mapped_data = json_decode(get_option("kk_plugin_mapped_data"),true);
            return $mapped_data;
          }
		
          if (empty(get_transient('kk_plugin_connection_down'))) {

                  if ($provider == 'klantenvertellen_api') {

                      $data = $this->get_klantenvertellen_api_data();
    				  
                      if (empty($data)) {
                          
                          $mapped_data = json_decode(get_option("kk_plugin_mapped_data"));
                          return $mapped_data;

                      }

                      $company_name = (isset($data->locationName) && !empty($data->locationName)) ? $data->locationName : get_bloginfo('name');

                      $total_score = str_replace(',', '.', $data->averageRating);

                      $mapped_data['total_score']	    = (float)$total_score;

                      $mapped_data['company_name'] 	    = $company_name;
                      $mapped_data['company_url'] 		= $data->viewReviewUrl;
                      $mapped_data['total_reviews'] 	= (int)$data->numberReviews;
    				  //$mapped_data['recommendation_perc'] 	= (int)$data->statistieken->percentageaanbeveling; // (n/a) Navragen bij Patrick
    				          
    				  // Since version 2.0.0 
    				  $mapped_data['categoryName'] = $data->categoryName;
    				  $mapped_data['street'] = $data->street;
    				  $mapped_data['houseNumber'] = $data->houseNumber;
					  $mapped_data['houseNumberExtension'] = (isset($data->houseNumberExtension) ? $data->houseNumberExtension : ' ');
    				  $mapped_data['postCode'] = $data->postCode;
    				  $mapped_data['city'] = $data->city;
    				  $mapped_data['country'] = $data->country;
    				  $mapped_data['starscount'][1] = (int)$data->oneStars;
    				  $mapped_data['starscount'][2] = (int)$data->twoStars;
    				  $mapped_data['starscount'][3] = (int)$data->threeStars;
    				  $mapped_data['starscount'][4] = (int)$data->fourStars;
    				  $mapped_data['starscount'][5] = (int)$data->fiveStars;
    				  
					  $mapped_data['last12MonthAverageRating'] 	= (float)$data->last12MonthAverageRating;
					  $mapped_data['last12MonthNumberReviews'] 	= (int)$data->last12MonthNumberReviews;
    				  $mapped_data['createReviewUrl'] = $data->createReviewUrl;
    				  $mapped_data['updatedSince'] = $data->updatedSince;
    				  $mapped_data['dateSince'] = $data->dateSince;
    				  $mapped_data['website'] = $data->website;
    				  $mapped_data['email'] = $data->email;
    				  $mapped_data['productId'] = $data->productId;

                      $mapped_data['reviews'] = array();

                      $review_count = 0;
    				  
    				  
    				
                      foreach ($data->reviews AS $review) {
    					  
                          $id = $review->reviewId;
    					  $date=$review->dateSince;
    					  
    					  $referenceCode = (isset($review->referenceCode) && !empty($review->referenceCode)) ? $review->referenceCode : null;
    					  $updatedSince = (isset($review->updatedSince) && !empty($review->updatedSince)) ? $review->updatedSince : $date;
    					  $name = (isset($review->reviewAuthor) && !empty($review->reviewAuthor)) ? $review->reviewAuthor : null;
    					  $city = (isset($review->city) && !empty($review->city)) ? $review->city : null;
    					  $rating = (isset($review->rating) && !empty($review->rating)) ? $review->rating : null;
    					  $lang = (isset($review->reviewLanguage) && !empty($review->reviewLanguage)) ? $review->reviewLanguage : 'nl';						  
						  $comment = (isset($review->reviewComments) && !empty($review->reviewComments)) ? $review->reviewComments : null;
    					  
    					
                          $mapped_data['reviews'][$review_count] = array(
                              'id' =>               $id,
                              'name' =>             $name,
                              'place' =>            $city,
                              'date' =>             $date,
                              'total_score' =>      $rating,
                              //'recommendation' =>   $recommendation, // n/a 
                              //'positive' =>         null, // n/a
                              'negative' =>         null, // n/a
                              'comment' =>          $comment, 
    						 // 'reviewContent'=> $reviewContent,
    						  'updatedSince'=>$updatedSince,
    						  'referenceCode'=>$referenceCode,
    						  'lang'=>$lang,
                          );
    					  
    					   foreach ($review->reviewContent AS $review_content) {
                    if(!isset($review_content->rating)) continue;
    						  
    								if ($review_content->questionGroup == 'DEFAULT_OVERALL') {
                                        $mapped_data['reviews'][$review_count]['overall'] =  (string)$review_content->rating;
                                    }
    								
    								if ($review_content->questionGroup == 'DEFAULT_OPINION') {
                                        $mapped_data['reviews'][$review_count]['positive'] =  (string)$review_content->rating;
                                    }

                                    if ($review_content->questionGroup == 'DEFAULT_RECOMMEND') {
                                        $mapped_data['reviews'][$review_count]['recommendation'] = ((string)$review_content->rating == 'true') ? 'yes' : 'no';
                                    }
    								
    								if ($review_content->questionGroup == 'DEFAULT_ONELINER') {
                                        $mapped_data['reviews'][$review_count]['one_liner'] = (string)$review_content->rating;
                                    }
    								if($review_content->questionGroup=='CUSTOM') {

    									$mapped_data['reviews'][$review_count]['custom'][(string)$review_content->order]=array(
    										'rating'=>(string)$review_content->rating,
    										'questionType'=>(string)$review_content->questionType,
    										'questionTranslation'=>(string)$review_content->questionTranslation,
    									);
    								}
    								
    								if($review_content->questionGroup=='CATEGORY' && isset($review_content->rating)) {
    									
    									$mapped_data['reviews'][$review_count]['category'][(string)$review_content->order]=array(
    										'rating'=>$review_content->rating,
    										'questionType'=>(string)$review_content->questionType,
    										'questionTranslation'=>$review_content->questionTranslation,
    									);
    									
    								}
                            }
    					  
    						
    					  
    					  if($review_count==0) {$mapped_data['last_review_date']=$date; }

                          $review_count++;

                          $positive = null; // Reset
                          $total_score = null; // Reset

                      }
    				


                  } elseif ($provider == 'kiyoh_api') {
    				  $data = $this->get_kiyoh_api_data();
    				          
                      if (empty($data)) {
                          
                          $mapped_data = json_decode(get_option("kk_plugin_mapped_data"),true);
                          return $mapped_data;

                      }

                      $company_name = (isset($data->locationName) && !empty($data->locationName)) ? $data->locationName : get_bloginfo('name');

                      $total_score = str_replace(',', '.', $data->averageRating);

                      $mapped_data['total_score']	    = (float)$total_score;

                      $mapped_data['company_name'] 	    = $company_name;
                      $mapped_data['company_url'] 		= $data->viewReviewUrl;
                      $mapped_data['total_reviews'] 	= (int)$data->numberReviews;
    				  //$mapped_data['recommendation_perc'] 	= (int)$data->statistieken->percentageaanbeveling; // (n/a) Navragen bij Patrick
    				  
    				  // Since version 2.0.0 
    				  $mapped_data['categoryName'] = $data->categoryName;
    				  $mapped_data['street'] = $data->street;
    				  $mapped_data['houseNumber'] = $data->houseNumber;
					  $mapped_data['houseNumberExtension'] = (isset($data->houseNumberExtension) ? $data->houseNumberExtension : ' ');
    				  $mapped_data['postCode'] = $data->postCode;
    				  $mapped_data['city'] = $data->city;
    				  $mapped_data['country'] = $data->country;
    				  $mapped_data['starscount'][1] = (int)$data->oneStars;
    				  $mapped_data['starscount'][2] = (int)$data->twoStars;
    				  $mapped_data['starscount'][3] = (int)$data->threeStars;
    				  $mapped_data['starscount'][4] = (int)$data->fourStars;
    				  $mapped_data['starscount'][5] = (int)$data->fiveStars;
    				  
					  $mapped_data['last12MonthAverageRating'] 	= (float)$data->last12MonthAverageRating;
					  $mapped_data['last12MonthNumberReviews'] 	= (int)$data->last12MonthNumberReviews;
    				  $mapped_data['createReviewUrl'] = $data->createReviewUrl;
    				  $mapped_data['updatedSince'] = $data->updatedSince;
    				  $mapped_data['dateSince'] = $data->dateSince;
    				  $mapped_data['website'] = $data->website;
    				  $mapped_data['email'] = $data->email;
    				  $mapped_data['productId'] = $data->productId;
					  

                      $mapped_data['reviews'] = array();

                      $review_count = 0;
    				  
                      foreach ($data->reviews AS $review) {
    					  
                          $id = $review->reviewId;
    					  $date=$review->dateSince;
    					  
    					  $referenceCode = (isset($review->referenceCode) && !empty($review->referenceCode)) ? $review->referenceCode : null;
    					  $updatedSince = (isset($review->updatedSince) && !empty($review->updatedSince)) ? $review->updatedSince : $date;
    					  $name = (isset($review->reviewAuthor) && !empty($review->reviewAuthor)) ? $review->reviewAuthor : null;
    					  $city = (isset($review->city) && !empty($review->city)) ? $review->city : null;
    					  $rating = (isset($review->rating) && !empty($review->rating)) ? $review->rating : null;
    					  $lang = (isset($review->reviewLanguage) && !empty($review->reviewLanguage)) ? $review->reviewLanguage : 'nl';
						  $comment = (isset($review->reviewComments) && !empty($review->reviewComments)) ? $review->reviewComments : null;    					  
    					
                          $mapped_data['reviews'][$review_count] = array(
                              'id' =>               $id,
                              'name' =>             $name,
                              'place' =>            $city,
                              'date' =>             $date,
                              'total_score' =>      $rating,
                              //'recommendation' =>   $recommendation, // n/a 
                              'positive' =>         '', // n/a
                              'negative' =>         null, // n/a
							  'comment' =>          $comment, 
    						 // 'reviewContent'=> $reviewContent,
    						  'updatedSince'=>$updatedSince,
    						  'referenceCode'=>$referenceCode,
    						  'lang'=>$lang,
                          );
    					  
    					   foreach ($review->reviewContent AS $review_content) {
                    if(!isset($review_content->rating)) continue;
    						  
    								if ($review_content->questionGroup == 'DEFAULT_OVERALL') {
                        $mapped_data['reviews'][$review_count]['overall'] =  (string)$review_content->rating;
                    }
    								
    								if ($review_content->questionGroup == 'DEFAULT_OPINION') {
                                        $mapped_data['reviews'][$review_count]['positive'] =  (string)$review_content->rating;
                                    }

                                    if ($review_content->questionGroup == 'DEFAULT_RECOMMEND') {
                                        $mapped_data['reviews'][$review_count]['recommendation'] = ((string)$review_content->rating == 'true') ? 'yes' : 'no';
                                    }
    								
    								if ($review_content->questionGroup == 'DEFAULT_ONELINER') {
                                        $mapped_data['reviews'][$review_count]['one_liner'] = (string)$review_content->rating;
                                    }
    								if($review_content->questionGroup=='CUSTOM') {

    									$mapped_data['reviews'][$review_count]['custom'][(string)$review_content->order]=array(
    										'rating'=>(string)$review_content->rating,
    										'questionType'=>(string)$review_content->questionType,
    										'questionTranslation'=>(string)$review_content->questionTranslation,
    									);
    								}
    								
    								if($review_content->questionGroup=='CATEGORY' && isset($review_content->rating)) {
    									
    									$mapped_data['reviews'][$review_count]['category'][(string)$review_content->order]=array(
    										'rating'=>$review_content->rating,
    										'questionType'=>(string)$review_content->questionType,
    										'questionTranslation'=>$review_content->questionTranslation,
    									);
    									
    								}
                  }
    					  
    						
    					  
    					  if($review_count==0) {$mapped_data['last_review_date']=$date; }

                          $review_count++;

                          $positive = null; // Reset
                          $total_score = null; // Reset

                      }
    				
                  } elseif ($provider == 'klantenvertellen') {

                      $data = $this->get_klantenvertellen_data();
    			             
                      if (empty($data)) {
                          
                          $mapped_data = json_decode(get_option("kk_plugin_mapped_data"),true);
                          return $mapped_data;

                      }

                      $company_name = (isset($this->plugin_settings['general']['klantenvertellen_company_name']) && !empty($this->plugin_settings['general']['klantenvertellen_company_name'])) ? $this->plugin_settings['general']['klantenvertellen_company_name'] : get_bloginfo('name');

                      $total_score = str_replace(',', '.', (string)$data->statistieken->gemiddelde);

                      $mapped_data['total_score']	    = (float)$total_score;
                      $mapped_data['company_name'] 	    = $company_name;
                      $mapped_data['company_url'] 		= (string)$this->provider_urls['klantenvertellen']['default'] . '/' . $this->plugin_settings['general']['klantenvertellen_slug'];
                      $mapped_data['total_reviews'] 	= (int)$data->statistieken->aantalbeoordelingen;
    				  $mapped_data['recommendation_perc'] 	= (int)$data->statistieken->percentageaanbeveling;
    				  
    				  /* Get averages from XML */
    				  $mapped_data['averages']=array();
    				  foreach ($data->statistieken->gemiddelden AS $averages) {
    					  
    					  foreach($averages AS $average) {
    						$type=$average->attributes();
    						$type_name = str_replace(':', '', $type['name']);
    						$average = (string)$average;

    						$mapped_data['averages'][$type_name]=$average;
    					  }
    				  }

                      $mapped_data['reviews'] = array();

                      $review_count = 0;
    				
                      foreach ($data->resultaten->resultaat AS $review) {

                          $id = (int)$review->id;
    					  $date=$name=$from=$total_score=$recommendation=$positive='';

                          foreach ($review->antwoord AS $antwoord) {

                              $attributes = $antwoord->attributes();

                              $antwoord = (string)$antwoord;

                              $attribute_name = strtolower(str_replace(':', '', $attributes['name']));

                              $review_field = 'ervaring'; // Default
                              $name_field = 'voornaam'; // Default

                              if (!empty($this->plugin_settings['general']['klantenvertellen_review_text_field']))
                                    $review_field = strtolower(str_replace(':', '', $this->plugin_settings['general']['klantenvertellen_review_text_field']));

                              if (!empty($this->plugin_settings['general']['klantenvertellen_name_field']))
                                    $name_field = strtolower(str_replace(':', '', $this->plugin_settings['general']['klantenvertellen_name_field']));
    						
                              switch ($attribute_name) {
                                  case 'datum': $date = $antwoord; break;
                                  case $name_field: $name = $antwoord; break;
                                  case 'uit': $from = $antwoord; break;
                                  case 'gemiddelde': $total_score = (float)str_replace(',', '.', $antwoord); break;
                                  case 'aanbeveling': $recommendation = $antwoord; break;
                                  case $review_field: $positive = $antwoord;
                              }
    						  
    						  $recommendation=strtolower($recommendation);
    						  if($recommendation=='ja') {$recommendation='yes'; } elseif($recommendation=='nee' || $recommendation=="") {$recommendation='no';}

                          }
    					  
                          $mapped_data['reviews'][$review_count] = array(
                              'id' =>               $id,
                              'name' =>             $name,
                              'place' =>            $from,
                              'date' =>             $date,
                              'total_score' =>      $total_score,
                              'recommendation' =>   $recommendation,
                              'positive' =>         $positive,
                              'negative' =>         null, // Not used for now
                              'comment' =>          (string)$review->reactie

                          );
    					  
    					  if($review_count==0) {$mapped_data['last_review_date']=$date; }

                          $review_count++;

                          $positive = null; // Reset
                          $total_score = null; // Reset

                      }


                  } elseif ($provider == 'klantenvertellen_v2') {

                      $data = $this->get_klantenvertellen_v2_data();

                      if (empty($data)) {
                          
                          $mapped_data = json_decode(get_option("kk_plugin_mapped_data"),true);
                          return $mapped_data;

                      }

                        $mapped_data['company_name'] = get_bloginfo('name');

                        $mapped_data['total_score']	    = (float)$data->averageRating;
    					if(!empty($data->locationName)) 
    						$mapped_data['company_name'] 	= (string)$data->locationName;
    						
    					$mapped_data['company_url'] 	= null;
    					
    					//$this->plugin_settings['general']['klantenvertellen_name_field']
    					
    					if (!empty($this->plugin_settings['general']['klantenvertellen_v2_company_url'])) {
    						$mapped_data['company_url']= esc_url($this->plugin_settings['general']['klantenvertellen_v2_company_url']);
    					}
    					
                        $mapped_data['total_reviews'] 	= (int)$data->numberReviews;
    					$mapped_data['recommendation_perc'] 	= (int)$data->percentageRecommendation;
    		  
    				  
    					/* No averages available at KV2 */
    					$mapped_data['averages']=array();
                        $mapped_data['reviews'] = array();
    					
                        $review_count = 0;

                        foreach ($data->reviews->reviews AS $review) {

                            $mapped_data['reviews'][$review_count] = array(
                                    'id' =>             (string)$review->reviewId,
                                    'name' =>             (string)$review->reviewAuthor,
                                    'place' =>            (string)$review->city,
                                    'date' =>             date('d-m-Y', strtotime((string)$review->dateSince)),
                                    'total_score' =>      (float)$review->rating
                            );
    						
    						if(isset($review->companyName)) 
    							$mapped_data['reviews'][$review_count]['company_name']=(string)$review->companyName;
    						

                            foreach ($review->reviewContent->reviewContent AS $review_content) {
    							
                                    if ($review_content->questionGroup == 'DEFAULT_OPINION') {
                                        $mapped_data['reviews'][$review_count]['positive'] =  (string)$review_content->rating;
                                    }

                                    if ($review_content->questionGroup == 'DEFAULT_RECOMMEND') {
                                        $mapped_data['reviews'][$review_count]['recommendation'] = ((string)$review_content->rating == 'true') ? 'yes' : 'no';
                                    }
    								
    								if ($review_content->questionGroup == 'DEFAULT_ONELINER') {
                                        $mapped_data['reviews'][$review_count]['one_liner'] = (string)$review_content->rating;
                                    }
    								if($review_content->questionGroup=='CUSTOM') {

    									$mapped_data['reviews'][$review_count]['custom'][(string)$review_content->order]=array(
    										'rating'=>(string)$review_content->rating,
    										'questionType'=>(string)$review_content->questionType,
    										'questionTranslation'=>(string)$review_content->questionTranslation,
    									);
    								}
                            }

                            //Deprecated since 1.1.8   $company_name = (string)$review->locationName; // Assume latest review location name = company name, for now
    						
    						if($review_count==0) {$mapped_data['last_review_date']=date('d-m-Y', strtotime((string)$review->dateSince)); }

                            $review_count++;

                            $review = null; // reset

                        }


                  } elseif ($provider == 'klantenvertellen_mobiliteit') {

                      $data = $this->get_klantenvertellen_mobiliteit_data();

                      if (empty($data)) {
                          
                          $mapped_data = json_decode(get_option("kk_plugin_mapped_data"),true);
                          return $mapped_data;

                      }

                        $mapped_data['company_name'] = (isset($this->plugin_settings['general']['klantenvertellen_mobiliteit_company_name']) && !empty($this->plugin_settings['general']['klantenvertellen_mobiliteit_company_name'])) ? $this->plugin_settings['general']['klantenvertellen_mobiliteit_company_name'] : get_bloginfo('name');

                        $total_score = str_replace(',', '.', (string)$data->totaal->gemiddelde);

                        $mapped_data['total_score']	    = (float)$total_score;
    					if(isset($data->company->name))
    						$mapped_data['company_name'] 	= (string)$data->company->name;
                        
    					$mapped_data['company_url'] 	= (string)$data->links->resultaten;
                        $mapped_data['total_reviews'] 	= (int)$data->statistieken->aantalingevuld;
    					$mapped_data['recommendation_perc'] 	= (int)$data->statistieken->aanbevolen;

    					/* Get averages from XML */
    					$mapped_data['averages']=array();
    					foreach ($data->totaal AS $averages) {
    						
    						foreach($averages AS $type=>$average) {
    							$average = (string)$average;
    							$mapped_data['averages'][$type]=$average;
    						}
    					}

                        $mapped_data['reviews'] = array();

                        $review_count = 0;

                        foreach ($data->beoordelingen->beoordeling AS $review) {
    						
    						if(isset($review->recommendation)) {
    							$review_rec=(string)$review->recommendation;
    						} else {
    							$review_rec=(string)$review->aanbeveling;
    						}

                            $mapped_data['reviews'][$review_count] = array(
                                    'id' =>             (int)$review->id,
                                    'name' =>             (string)$review->voornaam,
                                    'place' =>            (string)$review->woonplaats,
                                    //'date' =>             date('d-m-Y', strtotime((string)$review->datum)),
    								'date' =>             date('d-m-Y', (string)$review->datum),
                                    'total_score' =>      (float)$review->gemiddelde,
                                    'recommendation' =>   ($review_rec == 'ja')?'yes':'no',
                                    'positive' =>         (string)$review->beschrijving,
                                    'negative' =>         null,
                                    'comment' =>          (string)$review->reactie,
                            );
    						
    						//if($review_count==0) {$mapped_data['last_review_date']=date('d-m-Y', strtotime((string)$review->datum)); }
    						if($review_count==0) {$mapped_data['last_review_date']=date('d-m-Y', (string)$review->datum); }
                            $review_count++;
                            $review = null; // reset

                        }

                  } else {

                      if ($provider == 'kiyoh_com') {
                          $data = $this->get_kiyoh_com_data();
                      } else {
                          $data = $this->get_kiyoh_data();
                      }

                      if (empty($data)) {
                          
                          $mapped_data = json_decode(get_option("kk_plugin_mapped_data"),true);
                          return $mapped_data;

                      }

                      $mapped_data['total_score']	    = (float)$data->company->total_score;
                      $mapped_data['company_name'] 	    = (string)$data->company->name;
                      $mapped_data['company_url'] 		= (string)$data->company->url;
                      $mapped_data['total_reviews'] 	= (int)$data->company->total_reviews;
    				  
    				  // Does not exists in XML
    				  // $mapped_data['recommendation_perc'] 	= '';

    				  /* Get averages from XML */
    				  $mapped_data['averages']=array();
    					foreach ($data->company->average_scores AS $questions) {
    						
    						foreach($questions AS $question) {
    							
    							foreach($question as $average) {
    							
    								if((int)$average->score > 0) {
    									$mapped_data['averages'][(string)$average->title]=(int)$average->score;
    								}
    							}
    							
    						}
    				  }
    				  
                      $mapped_data['reviews'] = array();

                      $review_count = 0;
    				  
                      foreach ($data->review_list->review AS $review) {
    					  
    					  $recommendation=strtolower((string)$review->recommendation);
    					  if($recommendation=='ja') {$recommendation='yes'; } elseif($recommendation=='nee' || $recommendation=="") {$recommendation='no';}
    					
                          $mapped_data['reviews'][$review_count] = array(
                              'id' =>               (int)$review->id,
                              'name' =>             (string)$review->customer->name,
                              'place' =>            (string)$review->customer->place,
                              'date' =>             (string)$review->customer->date,
                              'total_score' =>      (float)$review->total_score,
                              'recommendation' =>   $recommendation,
                              'positive' =>         (string)$review->positive,
                              'negative' =>         (string)$review->negative,
                              'comment' =>          (string)$review->reaction,

                          );
    					  
    					  if($review_count==0) {$mapped_data['last_review_date']=(string)$review->customer->date; }

                          $review_count++;

                          $review = null; // reset

                      }

                  }

                  // set_transient( $transient_name, $mapped_data, 24 * HOUR_IN_SECONDS );
					
                  $dataSaved = update_option("kk_plugin_mapped_data", wp_json_encode($mapped_data));
                  set_transient( 'kk_plugin_last_check', current_time('timestamp'), 24 * HOUR_IN_SECONDS );
              }

          return $mapped_data;

        }

        else {
			
		  //echo date('Y-m-d H:i',get_transient('kk_plugin_last_check')).'<br>';
		  
		  $data=get_option("kk_plugin_mapped_data");
		  if(is_array($data)) {
			  return $data;
		  } else {
			return json_decode($data,true);
		  }
          
        }

      }

      /**
       * Render stars
       *
       * @return void
       */

     public static function render_star_rating($rating,$return=false)
     {

         $stars = 5; // Amount of stars to show
         $divide_by = (10/$stars);
         $rounded_by_half = round(($rating/$divide_by) * 2) / 2;
         $full_stars = floor($rounded_by_half);
         $show_half = (($rounded_by_half-$full_stars) != 0);
         $star_elements = '';

         for ($i = 1; $i <= $stars; $i++) {

             if ($i <= $full_stars) {
                 $class = 'star-full';
             } elseif ($i == $full_stars + 1 && $show_half) {
                 $class = 'star-half';
             } else {
                 $class = 'star-empty';
             }

             $star_elements .= '<span class="star '.$class.'"></span>';
        }
		if($return==true) {
			ob_start();
			include(self::get_template( 'star-rating.php' ));
			$stars=ob_get_contents();
			ob_end_clean();
			return $stars;
		} else {
			include(self::get_template( 'star-rating.php' ));
		}

     }

     /**
      * Render a single review
      *
      * @param array $review_data
      *
      * @return void
      */

    public static function render_single_review($review_data, $options = null)
    {
       include(self::get_template( 'single-review.php' ));
    }

    /**
     * Get logo from selected provider
     *
     * @param string $provider Provider
     * @return string Logo url
     */

    public static function get_logo($provider, $options = null)
    {
        if (isset($options['logo_type']) && $options['logo_type'] == 'full') {
            if (strpos($provider, 'kiyoh') !== false) {
                return KK_PLUGIN_DIR_URL.'assets/images/logos/kiyoh-full-logo.png';
            } else {
                return KK_PLUGIN_DIR_URL.'assets/images/logos/klantenvertellen-full-logo.png';
            }
        }
        return KK_PLUGIN_DIR_URL.'assets/images/logos/logo.png';
    }

    /**
     * Get name from selected provider
     *
     * @param string $provider Provider
     * @since 1.0.2
     * @return string Name
     */

    public static function get_name($provider)
    {

        switch ($provider) {
            case 'klantenvertellen_mobiliteit':
                return 'Klantenvertellen Mobiliteit';
                break;
			case 'klantenvertellen_api':
            case 'klantenvertellen_v2':
                return 'Klantenvertellen';
                break;
			case 'kiyoh_api':
            case 'kiyoh_com':
                return 'KiyOh';
                break;
            default:
                return ucfirst($provider);
        }

    }

    /**
     * Clear the transients
     */

    public static function clear_transients()
    {
		
		delete_transient('kk_plugin_kiyoh');
		delete_transient('kk_plugin_kiyoh_api');
		delete_transient('kk_plugin_kiyoh_com');
		delete_transient('kk_plugin_klantenvertellen');
		delete_transient('kk_plugin_klantenvertellen_api');
		delete_transient('kk_plugin_klantenvertellen_mobiliteit');
		delete_transient('kk_plugin_klantenvertellen_v2');
		delete_transient('kk_plugin_connection_down');

		delete_transient('kk_plugin_connection_kiyoh');
		delete_transient('kk_plugin_connection_kiyoh_api');
		delete_transient('kk_plugin_connection_kiyoh_com');
		delete_transient('kk_plugin_connection_klantenvertellen');
		delete_transient('kk_plugin_connection_klantenvertellen_api');
		delete_transient('kk_plugin_connection_klantenvertellen_mobiliteit');
		delete_transient('kk_plugin_connection_klantenvertellen_v2');
	  
		delete_transient('kk_plugin_last_check');
		
		update_option('kk_plugin_mapped_data', '');
    }

     /**
      * Do the request, process xml and return xml as an array
      *
      * @param string $url URL to get response for
      *
      * @return SimpleXMLElement Response body as SimpleXMLElement object
      */

     private function do_request($url)
     {

        $response = wp_remote_get( $url );

        if (is_wp_error($response)) {

            echo $response->get_error_message(); // TODO Make proper error handling function

        } else {

            $body = wp_remote_retrieve_body($response);
			
            if (!empty($body)) {
                libxml_use_internal_errors(true);
                if ($xml = @simplexml_load_string($body)) {
					
					return $xml;
                } else {
					if(!is_admin()) return true;
					if( $this->get_debug_status() ) {
						foreach (libxml_get_errors() as $error) {
							echo '<pre>'.__('Error', 'kk_plugin').': '.$error->message.'</pre>'; // TODO Make proper error handling function
						}
					}
                    libxml_clear_errors();
                }
            }

         }

     }

	 /**
	  * Shows links under plugin name in plugin overview page
	  *
	  */

	 public function plugin_links($links)
	 {
		$new = array(
			'settings' => '<a href="'.admin_url('options-general.php?page='.KK_PLUGIN_ID).'">'.__('Settings', 'kk_plugin').'</a>',
		);
		return wp_parse_args($links, $new);
	 }

     /**
      * Review length limit filter
      *
      * @param string $review_text Review text
      * @param array $review_data Review data for single review
      * @param array $options Widget or shortcode options
      *
      * @since 1.0.2
      * @return string
      */

     public function limit_single_review_length($review_text, $review_data, $options)
     {

         if (isset($options['limit_review_length']) && !empty($options['limit_review_length'])) {
             return self::trim_text($review_text, $options['limit_review_length']);
         }

         return $review_text;

     }
	 
	 /**
      * Format single review title filter
      *
      * @param string $review_title Review title
      * @param array $review_data Review data for single review
      * @param array $options Widget or shortcode options
      *
      * @since 1.0.2
      * @return string
	  * @Deprecated since 2.0.1
      

     public function format_single_review_title($default, $review_data, $options)
     {
         return $default;
     }
	 */
	 
	 /**
      * Format single review recommendation filter
      *
      * @param string $recommendation_answer yes or no
      * @param array $review_data Review data for single review
      * @param array $options Widget or shortcode options
      *
      * @since 1.0.2
      * @return string
      */

	 public function format_single_review_recommendation($return,$recommendation_answer, $review_data, $options)
     {

		if(strtolower($recommendation_answer)=='yes') $recommendation_answer=__('Yes','kk_plugin');
		if(strtolower($recommendation_answer)=='no') $recommendation_answer=__('No','kk_plugin');
		 
		$recommendation_text='<span>'.__('Would you recommend us?','kk_plugin').'</span>';
		$recommendation_text.=' <span class="kk-rating">'.__($recommendation_answer).'</span>';
        return $recommendation_text;

     }
	 

     /**
      * Trim text based on amount of words
      *
      * @param string $text
      * @param int $limit Amount of words to limit by
      *
      * @return string Truncated text
      */

     public static function trim_text($text, $limit)
     {

         $arr = explode(" ", $text);

         if (count($arr) >= $limit) {
             $new_arr = array_slice($arr, 0, $limit);
             return implode(" ", $new_arr).' [...]';
         }

         return $text;

     }

     /*public function create_cron()
     {

        if (! wp_next_scheduled ( 'cron_send_order_invite_emails' )) {
             wp_schedule_event(time(), 'hourly', 'cron_send_order_invite_emails');
        }


     }*/

     public function maybe_send_invite_email($order_id)
     {

         if (isset($this->plugin_settings['emails']['emails_enabled']) && $this->plugin_settings['emails']['emails_enabled'] == 1 && empty($this->plugin_settings['emails']['emails_send_after'])) {
             $this->send_review_invite_email($order_id);
         }

     }

     public function cron_send_order_invite_emails()
     {

         if (isset($this->plugin_settings['emails']['emails_enabled']) && $this->plugin_settings['emails']['emails_enabled'] == 1 && is_numeric($this->plugin_settings['emails']['emails_send_after']) && $this->plugin_settings['emails']['emails_send_after'] > 0) {

            $emails_send_after=$this->plugin_settings['emails']['emails_send_after'];

             $orders = wc_get_orders(array(
                'date_completed' => (time() - (DAY_IN_SECONDS * ($emails_send_after+5))).'...'.(time() - (DAY_IN_SECONDS * $emails_send_after)), // +5 to prevent (very) old orders from geting mailed
                'status' => 'completed',
                'kk_review_email_sent' => 'no',
             ));

             foreach ($orders AS $order) {

                 $this->send_review_invite_email($order->get_id());

             }

         }

     }

     public function custom_order_query_var($query, $query_vars)
     {

         if ( ! empty( $query_vars['kk_review_email_sent'] ) ) {
    		$query['meta_query'][] = array(
    			'key' => 'kk_review_email_sent',
    			'compare' => 'NOT EXISTS',
    		);
    	}

    	return $query;

     }

     /**
      * Send review invite email
      */

     public function send_review_invite_email($order_id)
     {

         global $sitepress;

         $order = wc_get_order($order_id);

         if ($order) {

             $review_email_sent = get_post_meta($order->get_id(), 'kk_review_email_sent', true);
             // Double check
             if (!empty($review_email_sent))
                return false;

             $allowed_roles = $this->plugin_settings['emails']['emails_send_to_roles'];

             if (empty($allowed_roles))
                return false;

             $order_user = $order->get_user();

             if (!$order_user && !in_array('wc_guest', $allowed_roles))
                return false;
             elseif ($order_user && count(array_diff($order_user->roles, $allowed_roles)) > 0)
                return false;


             $send=apply_filters('kk_plugin_must_send_mail',true,$order);
             if($send==false) 
                return false;
            
             
            $subject = apply_filters('kk_plugin_email_subject',$this->plugin_settings['emails']['emails_subject'],$order);


            $body = apply_filters('kk_plugin_email_body',$this->plugin_settings['emails']['emails_body'],$order);


            // WPML
            if (!empty($sitepress)) {
                $order_lang = get_post_meta($order->get_id(), 'wpml_language', true);

                if (!empty($order_lang)) {
                    $current_lang = $sitepress->get_current_language();
                    $sitepress->switch_lang($order_lang);

                    $kk_email_data = get_option($this->settings_ids['emails']);
                    $subject = $kk_email_data['emails_subject'];
                    $body = $kk_email_data['emails_body'];
                    $sitepress->switch_lang($current_lang);
                }


            }
			
			$body = $this->search_replace_customer($body,$order_id);
			
			$body = wpautop(make_clickable($body, 'kk_plugin'));

            $mailer = WC()->mailer();
            //format the email
            $recipient = $order->get_billing_email();
            $subject = $subject;
            $content = wc_get_template_html( 'emails/invite-email.php', array(
        		'order'         => $order,
        		'email_heading' => $subject,
                'body'          => $body,
        		'sent_to_admin' => false,
        		'plain_text'    => false,
        		'email'         => $mailer
        	), KK_PLUGIN_VIEWS_PATH.'/woocommerce/', KK_PLUGIN_VIEWS_PATH.'/woocommerce/' );
            $headers = "Content-Type: text/html\r\n";
            //send the email through wordpress
            if ($mailer->send( $recipient, $subject, $content, $headers )) {
                update_post_meta($order->get_id(), 'kk_review_email_sent', current_time('mysql'));
            }

         }

     }

     public static function is_woocommerce_activated() {
		return class_exists( 'woocommerce' );
	 }
	 
	public function plugin_update_message( $data, $response ) {
		if( isset( $data['upgrade_notice'] ) ) {
			echo $data['upgrade_notice'];
		}
	}
	
	protected function search_replace_customer($body,$order_id) {
		$order = wc_get_order($order_id);
		
		$items = $order->get_items();
		$product_ids='';
		foreach ( $items as $item ) {
			$product_ids.= '&productID[]='.$item->get_product_id();
		
		}

		$product_ids=ltrim($product_ids, '&');
		
		$replaces=array(
			'{{name}}'=>$order->get_formatted_billing_full_name(),
			'{{first_name}}'=>$order->get_billing_first_name(),
			'{{last_name}}'=>$order->get_billing_last_name(),
			'{{email}}'=>$order->get_billing_email(),
			'{{city}}'=>$order->get_billing_city(),
			'{{product_ids}}'=>$product_ids,
			'{{order_date}}'=>date_i18n( get_option( 'date_format' ), strtotime( $order->get_date_created())),
			'{{completed_date}}'=> date_i18n( get_option( 'date_format' ), strtotime($order->get_date_completed())),
		);
		
		foreach($replaces as $search => $replace) {
			$body=str_replace($search,$replace,$body);
		}
		
		return $body;
		
	}
	
	
	/**
	* Locate template.
	*
	* Locate the called template.
	* Search Order:
	* 1. /themes/theme/kiyoh-klantenvertellen/$template_name
	* 2. /plugins/kiyoh-klantenvertellen/templates/$template_name.
	*
	* @since 1.2.0
	*
	* @param 	string 	$template_name			Template to load.
	* @param 	string 	$string $template_path	Path to templates.
	* @param 	string	$default_path			Default path to template files.
	* @return 	string 							Path to the template file.
	*/
	private static function locate_template( $template_name, $template_path = '', $default_path = '' ) {
		// Set variable to search in woocommerce-plugin-templates folder of theme.
		if ( ! $template_path ) :
			$template_path = 'kiyoh-klantenvertellen/';
		endif;
		// Set default plugin templates path.
		if ( ! $default_path ) :
			$default_path = KK_PLUGIN_DIR_PATH . 'templates/'; // Path to the template folder
		endif;
		// Search template file in theme folder.
		$template = locate_template( array(
			$template_path . $template_name,
			$template_name
		) );
		// Get plugins template file.
		if ( ! $template ) :
			$template = $default_path . $template_name;
		endif;
		return apply_filters( 'kk_locate_template', $template, $template_name, $template_path, $default_path );
	}
	
	/**
	* Get template.
	*
	* Search for the template and include the file.
	*
	* @since 1.2.0
	*
	* @see locate_template()
	*
	* @param string 	$template_name			Template to load.
	* @param array 	$args					Args passed for the template file.
	* @param string 	$string $template_path	Path to templates.
	* @param string	$default_path			Default path to template files.
	*/
	public static function get_template( $template_name, $args = array(), $tempate_path = '', $default_path = '' ) {
		if ( is_array( $args ) && isset( $args ) ) :
			extract( $args );
		endif;
		$template_file = self::locate_template( $template_name, $tempate_path, $default_path );
		if ( ! file_exists( $template_file ) ) :
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
			return;
		endif;
		return $template_file;
	}
	
	public static function format_custom_review_data($format,$data) {
		preg_match_all('/{CUSTOM}(.*?){\/CUSTOM}/s', $format, $matches);
		if(isset($matches[1])) {
			foreach($matches[1] as $question) {
				$question=explode(':',$question);
				if(isset($question[1])) {
					$replace=$data[$question[0]][$question[1]];
					if($replace=='') $replace=__('n/a','kk_plugin');
					$format=str_replace('{CUSTOM}'.$question[0].':'.$question[1].'{/CUSTOM}',$replace,$format);
				}
				
			}
		}
		return $format;
	}
	
	public function create_old_xml_backup() {
		
		
		if(!is_admin()) return false;
		
		$provider = $this->plugin_settings['general']['kk_provider'];
		if($provider!="klantenvertellen_api" && $provider!="kiyoh_api" && get_transient('kk_plugin_backup')==false) {
			$data = $this->get_data($provider);
			$backup_data=get_option( 'kk_plugin_backup_'.$provider);
			
			if(isset($data['reviews']) && !empty($data['reviews'])) {
				if($data!=$backup_data && !empty($data)) {
					update_option( 'kk_plugin_backup_'.$provider, $data );
				} 
				set_transient( 'kk_plugin_backup', current_time('timestamp'), 1 * HOUR_IN_SECONDS );
			}
		}
		return true;
	}
	
	/**
     * Get Klantenvertellen API data
     *
     * @since 2.0.0
     * @return string|false Data or false
     */

    public function get_klantenvertellen_api_data()
    {

  		if (isset($this->plugin_settings['general']['klantenvertellen_api_token']))
  			$token = $this->plugin_settings['general']['klantenvertellen_api_token'];
  		if (isset($this->plugin_settings['general']['klantenvertellen_api_locationId']))
  			$locationId = $this->plugin_settings['general']['klantenvertellen_api_locationId'];
  		/*if (isset($this->plugin_settings['general']['klantenvertellen_api_tenantId']))
  			$tenantId = $this->plugin_settings['general']['klantenvertellen_api_tenantId'];
            */ 
  		if (!empty($token) && !empty($locationId)) {

			$request_url = $this->provider_urls['klantenvertellen']['api'].'?locationId='.$locationId.'&tenantId=99';
			

      		$data=self::CallAPI('GET',$request_url,false,$token);
      	
  		if($data) {
		    $data=json_decode($data);
		  return $data;

  		}
  		return false;

      } 
      else {
  	     return false;
      }

    }
	
	/**
     * Get KiyOh API data
     *
     * @since 2.0.0
     * @return string|false Data or false
     */

    public function get_kiyoh_api_data()
    {

        
		if (isset($this->plugin_settings['general']['kiyoh_api_token']))
			$token = $this->plugin_settings['general']['kiyoh_api_token'];
		if (isset($this->plugin_settings['general']['kiyoh_api_locationId']))
			$locationId = $this->plugin_settings['general']['kiyoh_api_locationId'];
		if (!empty($token) && !empty($locationId)) {
			$request_url = $this->provider_urls['kiyoh']['api'].'?locationId='.$locationId.'&tenantId=98';
			
			$data=self::CallAPI('GET',$request_url,false,$token);
			if($data) {
        $data=json_decode($data);
      return $data;

        } else {
			return false;
        }

    }
	
  }
	
	private static function CallAPI($method, $url, $data = false,$token)
	{
		$result=false;
		if ( false === ( $connection_time = get_transient( 'kk_plugin_connection_down' ) ) ) {
		$curl = curl_init();
		
			switch ($method)
			{
				case "POST":
					curl_setopt($curl, CURLOPT_POST, 1);
	
					if ($data)
						curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
					break;
				case "PUT":
					curl_setopt($curl, CURLOPT_PUT, 1);
					break;
				default:
					if ($data)
						$url = sprintf("%s?%s", $url, http_build_query($data));
			}
	
			// Optional Authentication:
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60); 
			curl_setopt($curl, CURLOPT_TIMEOUT, 400);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				'x-Publication-Api-Token: '.$token,
				'Content-Type: application/json',
			));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	   
			$result = curl_exec($curl);

      		$resultCheck = json_decode($result);
			

			if ($result==false || !($resultCheck->numberReviews) > 0 ) {
				
      			set_transient( 'kk_plugin_connection_down', current_time('timestamp'), 6 * HOUR_IN_SECONDS );
				curl_close($curl);
       			return false;

			}
      
			curl_close($curl);
		}
		return $result;
	}
	
	public static function output_custom_data($data) {
		$type=$data['questionType'];
		$rating=$data['rating'];
		$label=$data['questionTranslation'];
		
		$provider=new KiyOh_Klantenvertellen_Plugin();
		$provider=$provider->plugin_settings['general']['kk_provider'];
		
		switch($type) {
			case 'STARS':
				$default='<li class="kk-question-'.$type.'-'.rand(1,999).'"><span class="kk-question-label">'.$label.'</span> '.self::render_star_rating($rating,true).'</li>';
				break;
			case 'IMAGE':
				if($provider=='klantenvertellen_api') {$domain='https://klantenvertellen.nl';} elseif($provider=='kiyoh_api') {$domain='https://kiyoh.nl';}
				$image=json_decode($rating);
				$thumb=esc_url($domain.$image->thumbnailPath);
				$url=esc_url($domain.$image->imagePath);
				$default='<li class="kk-question-'.$type.'-'.rand(1,999).'"><span class="kk-question-label">'.$label.'</span> <a class="kk-question-image-link" href="'.$url.'" target="_blank"><img class="kk-question-image" src="'.$thumb.'" alt="'.$label.'"></a></li>';
				break;
			case 'BOOLEAN':
				if($rating) {
					$yes_no=__('Yes','kk_plugin');
				} else {
					$yes_no=__('No','kk_plugin');
				}
				$default='<li class="kk-question-'.$type.'-'.rand(1,999).'"><span class="kk-question-label">'.$label.'</span> <span class="kk-rating">'.$yes_no.'</span></li>';
				break;
			case 'SELECT_LIST':
				$options=implode(', ',json_decode($rating));
				$default='<li class="kk-question-'.$type.'-'.rand(1,999).'"><span class="kk-question-label">'.$label.'</span> <span class="kk-rating">'.$options.'</span></li>';
				break;
			default:
				$default='<li class="kk-question-'.$type.'-'.rand(1,999).'"><span class="kk-question-label">'.$label.'</span> <span class="kk-rating">'.$rating.'</span></li>';
		}
		
		return apply_filters('kk_custom_data',$default,$label,$rating,$type);
	}
	
	public static function show_single_review_divider($show_divider=false,$review_data) {
		if($show_divider==true) {
			echo '<span class="kk-single-review-meta-divider">'.apply_filters('kk_plugin_single_review_meta_divider', '-', $review_data).'</span>';
		}
	}
	
	public function api_admin_notice() {
		if(!is_admin()) return true;
		$provider = $this->plugin_settings['general']['kk_provider'];
		if($provider!="klantenvertellen_api" && $provider!="kiyoh_api") {		
		?>
		<div class="notice notice-warning is-dismissible">
			<p><?php printf(__('%s will stop using the old XML method from March 2020.','kk_plugin'),'KiyOh / Klantenvertellen'); ?> <a href="<?php echo get_admin_url();?>/options-general.php?page=kiyoh_klantenvertellen"><?php _e('Please switch to the new API method on time.','kk_plugin');?></a></p>
		</div>
		<?php
		}
	}
	
	public function get_debug_status() {
		$debug= (isset($this->plugin_settings['default_settings']['debug']))?$this->plugin_settings['default_settings']['debug']:0;
		return $debug;
	}

}
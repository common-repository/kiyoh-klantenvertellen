<?php
/**
 * Shortcode display for the KiyOh Klantenvertellen plugin
 *
 * @version 2.0.0
 * @package KiyOh_Klantenvertellen
 * @author Bart Pluijms
 */

namespace KiyOh_Klantenvertellen;

class Shortcode {

    private $main_plugin;

    /**
     * @param KiyOh_Klantenvertellen_Plugin $main_plugin_class
     */
    public function __construct($main_plugin_class)
    {
        $this->main_plugin = $main_plugin_class;

        add_shortcode('kiyoh-klantenvertellen', array( $this, 'render_shortcode') );
        add_shortcode('kiyoh-klantenvertellen-single-review', array( $this, 'render_single_review') );
        add_shortcode('kiyoh-klantenvertellen-summary', array( $this, 'render_summary') );
		add_shortcode('kiyoh-klantenvertellen-stats', array( $this, 'render_stats') );
		add_shortcode('kiyoh-klantenvertellen-average', array( $this, 'render_average') );
    }

    /**
     * Render shortcode
     *
     * @return array $atts Shortcode attributes
     * @return string Shortcode output
     */

	public function render_shortcode( $atts )
    {
        $default_atts = $atts;
        // Fill defaults
        $fields = Widget::get_widget_fields();

        foreach ($fields AS $field) {
            $defaults[$field['id']] = $field['default'];
        }

        // Overwrite with user default settings
        if (isset($this->main_plugin->plugin_settings['default_settings']) && !empty($this->main_plugin->plugin_settings['default_settings'])) {
            $defaults = $this->main_plugin->plugin_settings['default_settings'];
        }

        // 'Convert' it to array (make it compatible with the widget options)
        if (isset($atts['hide_without_rating'])) {
            if ($atts['hide_without_rating'] == 'yes') {
                $atts['review_display_options']['hide_without_rating'] = 1;
            } elseif($atts['hide_without_rating'] == 'no') {
                $atts['review_display_options']['hide_without_rating'] = 0;
            }
        }

		if (isset($atts['hide_without_rating_text'])) {
            if ($atts['hide_without_rating_text'] == 'yes') {
                $atts['review_display_options']['hide_without_rating_text'] = 1;
            } elseif($atts['hide_without_rating_text'] == 'no') {
                $atts['review_display_options']['hide_without_rating_text'] = 0;
            }
        }

        if (isset($atts['hide_without_author'])) {
            if ($atts['hide_without_author'] == 'yes') {
                $atts['review_display_options']['hide_without_author'] = 1;
            } elseif($atts['hide_without_author'] == 'no') {
                $atts['review_display_options']['hide_without_author'] = 0;
            }
        }
		
		$atts = shortcode_atts( $defaults, $atts, 'kiyoh-klantenvertellen' );
		
		if(!isset($atts['class'])) $atts['class']='';
		
        if (!empty($default_atts)) {
            $atts = array_merge($default_atts, $atts);
        }

		if(!empty($atts['color']))
			$atts['txtcolor']=(hexdec($atts['color']) > 0xffffff/2) ? 'black':'white';

		return $this->main_plugin->get_layout($atts['layout'], $atts);

    }

    public function render_single_review($atts)
    {
		if(!isset($atts['layout'])) $atts['layout']='default';

        if (isset($atts['id']) && !empty($atts['id'])) {
            $provider = $this->main_plugin->plugin_settings['general']['kk_provider'];
            $data = $this->main_plugin->get_data($provider);
            $the_review = false;
            if (is_array($data['reviews']) && count($data['reviews'])) {
                foreach ($data['reviews'] AS $single_review) {
                    if ($single_review['id'] == $atts['id']) {
                        $the_review = $single_review;
                        break;
                    }
                }
            }
            if(!isset($atts['class'])) $atts['class']='kk-default';			

			return $this->render_shortcode_content('single-review', array(
                'review_data' => $the_review,
                'options' => $atts,
            ));

        } else {
            return __('No ID specified', 'kk_plugin');
        }
    }

    public function render_summary($atts, $content)
    {
		$provider = $this->main_plugin->plugin_settings['general']['kk_provider'];

        $data = $this->main_plugin->get_data($provider);

        if (isset($atts['show_stars']) && $atts['show_stars'] == 'yes') {
            $show_stars = true;
        } else {
            $show_stars = false;
        }

		$stars_theme='yellow-grey';
	
		if (isset($atts['stars_theme'])) {
            $stars_theme=esc_attr($atts['stars_theme']);
		}
	
		return $this->render_shortcode_content('summary', array(
            'data' => $data,
            'show_stars' => $show_stars,
			'stars_theme' => $stars_theme,
        ));
    }

	public function render_average($atts, $content)
    {
		$provider = $this->main_plugin->plugin_settings['general']['kk_provider'];

        $data = $this->main_plugin->get_data($provider);

        return $this->render_shortcode_content('average', array(
            'data' => $data,
        ));
    }

	public function render_stats($atts, $content)
    {
		$provider = $this->main_plugin->plugin_settings['general']['kk_provider'];
        $data = $this->main_plugin->get_data($provider);
		$logo=wp_get_attachment_image_src(get_theme_mod('custom_logo'),'full');
		if(isset($logo[0])) {
			$data['logo']=$logo[0];
		}

		$atts = shortcode_atts(
			array(
				'style' => 'light',
				'bgcolor' => '',
				'txtcolor' => '',
				'share' => '',
				'class' => '',
			), 
			$atts, 
			'kiyoh-klantenvertellen-stats' 
		);
		
		if(empty($data['averages']) && $atts['share']!='yes') {
			$box =2;
		} elseif(empty($data['averages']) && $atts['share']=='yes') {
			$box= 3;
		} elseif(!empty($data['averages']) && $atts['share']!='yes') {
			$box= 4;
		} else {
			$box= 5;
		}

        return $this->render_shortcode_content('stats', array(
            'data' => $data,
			'style'=>$atts['style'],
			'bgcolor'=>$atts['bgcolor'],
			'txtcolor'=>$atts['txtcolor'],
			'box'=>$box,
			'share'=>$atts['share'],
        ));

    }

    /*
     * Puts output in buffering and return contents
     *
     * @param string $template_name The template filename to be used
     * @param array Variables passed to the template file
     * @return string Template contents
     */

	private function render_shortcode_content($template_name, $params = array())
	{
       global $post;
       extract($params); // Array is now available as variable (array key = variable)
       ob_start();

	   $main=new KiyOh_Klantenvertellen_Plugin();

	   include($main->get_template( 'shortcodes/'.$template_name.'.php'));

       $return = ob_get_contents();

       ob_end_clean();

       return $return;
   }

}
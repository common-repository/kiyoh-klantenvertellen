<?php
/**
 * Settings page for the KiyOh Klantenvertellen plugin
 *
 * @version 2.0.0
 * @package KiyOh_Klantenvertellen
 * @author Bart Pluijms
 */

namespace KiyOh_Klantenvertellen;

class Settings_Page {

    /**
     * @param KiyOh_Klantenvertellen_Plugin $main_plugin_class
     */
    private $main_plugin;

    /** @var array $klantenvertellen_review_attributes Possible review attributes */
    private $klantenvertellen_review_attributes;

    /** @var array $settings_ids Setting IDs, used to save settings from different tabs */
    private $settings_ids = array();

    public function __construct($main_plugin_class)
    {

        $this->main_plugin = $main_plugin_class;

        // Set the settings ids
        $this->settings_ids = $this->main_plugin->settings_ids;

        add_action( 'admin_menu', array($this, 'add_admin_menu') );
        add_action( 'admin_init',  array($this, 'register_my_plugin_settings') );
        add_action( 'admin_init',  array($this, 'catch_clear_transient') );
        add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_scripts') );

        add_action('updated_option', array($this, 'clear_transients'), 10, 1);
        add_action('added_option', array($this, 'clear_transients'), 10, 1);

		

    }

    /**
     * Enqueue admin scripts & styles for the settings page
     *
     * @return boolean|void
     */

    public function enqueue_admin_scripts($hook)
    {

        // Only load on settings page
        if ($hook != 'settings_page_'.KK_PLUGIN_ID)
            return false;

        wp_enqueue_script( KK_PLUGIN_ID.'_admin', KK_PLUGIN_DIR_URL . 'assets/admin/admin.js', array('jquery') );
        wp_register_style( KK_PLUGIN_ID.'_admin_style', KK_PLUGIN_DIR_URL.'assets/admin/admin.css' );
        wp_enqueue_style( KK_PLUGIN_ID.'_admin_style' );

    }

    /**
     * Add an extra option to the settings tab in the WP admin
     *
     * @return void
     */

    public function add_admin_menu(  ) {

    	add_submenu_page( 'options-general.php', __('KiyOh / Klantenvertellen', 'kk_plugin'), __('KiyOh / Klantenvertellen', 'kk_plugin'), 'manage_options', KK_PLUGIN_ID, array($this,'render_options_page') );

    }

    /**
     * Register plugin settings
     *
     * @return void
     */

    public function register_my_plugin_settings()
    {
		
		// Register all settings
        foreach ($this->settings_ids AS $setting_id) {
            register_setting( $setting_id, $setting_id );
        }

        $this->register_common_settings();
        $this->register_kiyoh_settings();
        $this->register_kiyoh_com_settings();
        $this->register_klantenvertellen_settings();
        $this->register_klantenvertellen_mobiliteit_settings();
        $this->register_klantenvertellen_v2_settings();
        $this->register_default_settings();
		$this->register_debug_settings();
        $this->register_emails_settings();

		$this->register_klantenvertellen_api_settings();
		$this->register_kiyoh_api_settings();

		$provider = $this->main_plugin->plugin_settings['general']['kk_provider'];
		if($provider=='klantenvertellen_api' || $provider=='kiyoh_api') {
			$this->register_single_review_settings();
		}
		
    }

    /**
     * Clear transient check
     */

    public function catch_clear_transient()
    {

        if (isset($_GET['page']) && $_GET['page'] == KK_PLUGIN_ID ) {


            if (isset($_GET['transient-cleared']) && $_GET['transient-cleared'] == 'true') {

                add_action( 'admin_notices', function() {
                    ?>
                    <div class="notice notice-success is-dismissible">
                       <p><?php _e( 'Cache successfully cleared', 'kk_plugin' ); ?></p>
                   </div>
                    <?php
                } );

            } elseif ((isset($_GET['clear-transient']) && $_GET['clear-transient'] == 'true') || (isset($_GET['settings-updated']) && $_GET['settings-updated']==true)) {

                KiyOh_Klantenvertellen_Plugin::clear_transients();
                wp_redirect(add_query_arg( array( 'transient-cleared' => 'true' ), admin_url( 'options-general.php?page='.KK_PLUGIN_ID ) ));
                exit;

            }

        }

    }

    /**
     * Clear transient on option change
     */

    public function clear_transients($option)
    {

        if ($option == 'kk_plugin_settings') {
            KiyOh_Klantenvertellen_Plugin::clear_transients();
        }

    }

    /**
     * Register common settings
     *
     * @return void
     */

    private function register_common_settings()
    {

        // Sections

    	add_settings_section(
    		'kk_plugin_common_section',
    		__( 'General', 'kk_plugin' ),
            false,
    		//array( $this, 'settings_section_callback' ),
    		$this->settings_ids['general']
    	);

        // Fields

        add_settings_field(
    		'kk_provider',
    		__( 'KiyOh or Klantenvertellen', 'kk_plugin' ),
    		array($this, 'render_provider_field'),
    		$this->settings_ids['general'],
    		'kk_plugin_common_section'
    	);

    }

    /**
     * Register emails settings
     *
     * @return void
     */

    private function register_emails_settings()
    {

        // Sections

    	add_settings_section(
    		'kk_plugin_emails_section',
    		__( 'E-mails', 'kk_plugin' ),
            false,
    		//array( $this, 'settings_section_callback' ),
    		$this->settings_ids['emails']
    	);

        // Fields


        add_settings_field(
    		'kk_emails_enabled',
    		__( 'Enable e-mail', 'kk_plugin' ),
    		array($this, 'render_email_enabled_field'),
    		$this->settings_ids['emails'],
    		'kk_plugin_emails_section'
    	);

        add_settings_field(
    		'kk_emails_subject',
    		__( 'E-mail subject', 'kk_plugin' ),
    		array($this, 'render_email_subject_field'),
    		$this->settings_ids['emails'],
    		'kk_plugin_emails_section'
    	);

        add_settings_field(
    		'kk_emails_body',
    		__( 'E-mail text', 'kk_plugin' ),
    		array($this, 'render_email_body_field'),
    		$this->settings_ids['emails'],
    		'kk_plugin_emails_section'
    	);

        add_settings_field(
    		'kk_emails_send_after',
    		__( 'Send e-mail after', 'kk_plugin' ),
    		array($this, 'render_email_send_after_field'),
    		$this->settings_ids['emails'],
    		'kk_plugin_emails_section'
    	);

        add_settings_field(
    		'kk_emails_send_to_roles',
    		__( 'Only send to', 'kk_plugin' ),
    		array($this, 'render_email_send_to_roles_field'),
    		$this->settings_ids['emails'],
    		'kk_plugin_emails_section'
    	);

    }

    /**
     * Register default settings
     *
     * Default widget / shortcode settings + example
     *
     * @return void
     */

    private function register_default_settings()
    {
        // Sections
        add_settings_section(
            'kk_plugin_default_settings_section',
            __( 'Layout settings', 'kk_plugin' ),
            array( $this, 'settings_section_callback' ),
            $this->settings_ids['default_settings']
        );
		

		// Fields, inherit from widget settings
		
		$provider = $this->main_plugin->plugin_settings['general']['kk_provider'];

		$fields = Widget::get_widget_fields($provider);
		foreach ($fields AS $field) {
			add_settings_field(
				$field['id'],
				$field['label'],
				array($this, 'render_default_settings_field'),
				$this->settings_ids['default_settings'],
				'kk_plugin_default_settings_section',
				array('field' => $field)
			);
		}

    }
	
	
    /**
     * Register debug settings
     *
     * @return void
     */

    private function register_debug_settings()
    {
        // Sections
        add_settings_section(
            'kk_plugin_debug_settings_section',
            __( 'Debug settings', 'kk_plugin' ),
            array( $this, 'settings_section_callback' ),
            $this->settings_ids['default_settings']
        );
		
		add_settings_field(
			'kk_debug',
			__('Debug modus','kk_plugin'),
			array($this, 'render_debug_settings_field'),
			$this->settings_ids['default_settings'],
			'kk_plugin_debug_settings_section',
			null
		);
		
		add_settings_field(
			'kk_debugid',
			__('Show review ID','kk_plugin'),
			array($this, 'render_debug_review_id_settings_field'),
			$this->settings_ids['default_settings'],
			'kk_plugin_debug_settings_section',
			null
		);

    }
	
	
	private function register_single_review_settings()
    {

        add_settings_section(
			'kk_plugin_single_review_settings_section',
			__( 'Single Review settings', 'kk_plugin' ),
			array( $this, 'settings_section_callback' ),
			 $this->settings_ids['single_review_settings']
		);
		
		$fields = API::get_widget_fields();
		
		foreach ($fields AS $field) {
			add_settings_field(
				$field['id'],
				$field['label'],
				array($this, 'render_default_settings_field'),
				$this->settings_ids['default_settings'], //default_settings
				'kk_plugin_'.$field['section'].'_section', //kk_plugin_default_settings_section
				array('field' => $field)
			);
		}
		
		$provider = $this->main_plugin->plugin_settings['general']['kk_provider'];				
		$data=$this->main_plugin->get_data($provider);
		
		
		$reviewFieldsCustom=$reviewFieldsCategory=array();
		$i=0;
		if(isset($data['reviews']) && !empty($data['reviews'])) {
			foreach($data['reviews'] as $review) {
				if(isset($review['category'])) {
					foreach($review['category'] as $key=>$category) {
						$reviewFieldsCategory[$key]=$category;
					}
				}
				
				if(isset($review['custom'])) {
					foreach($review['custom'] as $key=> $custom) {
						$reviewFieldsCustom[$key]=$custom;
					}
					
				}
				$i++;
				if($i==10) break;
			}
		}
		if(!empty($reviewFieldsCategory)) {
		foreach($reviewFieldsCategory as $key=>$reviewField) {
			
			$field['label']=sprintf(__('Show: "%s"','kk_plugin'),$reviewField['questionTranslation']);
			$field['type']='disabled';
			$field['description']=sprintf(__('Question type: %s','kk_plugin'),$this->translateType($reviewField['questionType']));
			add_settings_field(
				rand(0,999),
				$field['label'],
				array($this, 'render_default_settings_field'),
				$this->settings_ids['default_settings'], //default_settings
				'kk_plugin_single_review_settings_section', //kk_plugin_default_settings_section
				array('field' => $field)
			);
			
			if($reviewField['questionType']=='INT') {
				
				$field['label']=sprintf(__('Show star rating for "%s"','kk_plugin'),$reviewField['questionTranslation']);
				$field['description']=sprintf(__('Show or hide star rating of question "%s".','kk_plugin'),$reviewField['questionTranslation']);
				
				add_settings_field(
					rand(0,999),
					$field['label'],
					array($this, 'render_default_settings_field'),
					$this->settings_ids['default_settings'], //default_settings
					'kk_plugin_single_review_settings_section', //kk_plugin_default_settings_section
					array('field' => $field)
				);
			}
			
		} 
		}
		if(!empty($reviewFieldsCustom)) {
		foreach($reviewFieldsCustom as $key=>$reviewField) {
			
			$field['label']=sprintf(__('Show: "%s"','kk_plugin'),$reviewField['questionTranslation']);
			$field['type']='disabled';
			$field['description']=sprintf(__('Question type: %s','kk_plugin'),$this->translateType($reviewField['questionType']));
			add_settings_field(
				rand(0,9999),
				$field['label'],
				array($this, 'render_default_settings_field'),
				$this->settings_ids['default_settings'], //default_settings
				'kk_plugin_single_review_settings_section', //kk_plugin_default_settings_section
				array('field' => $field)
			);
			
			if($reviewField['questionType']=='INT') {
				
				$field['label']=sprintf(__('Show star rating for "%s"','kk_plugin'),$reviewField['questionTranslation']);
				$field['description']=sprintf(__('Show or hide star rating of question "%s".','kk_plugin'),$reviewField['questionTranslation']);
				
				add_settings_field(
					rand(0,9999),
					$field['label'],
					array($this, 'render_default_settings_field'),
					$this->settings_ids['default_settings'], //default_settings
					'kk_plugin_single_review_settings_section', //kk_plugin_default_settings_section
					array('field' => $field)
				);
			}
			
		} 
		}

    }

	/**
     * Add the KiyOh settings options
     *
     * @return void
     */
	private function register_kiyoh_settings()
    {

        // Sections
        add_settings_section(
    		'kk_plugin_kiyoh_section',
    		__( 'KiyOh settings', 'kk_plugin' ),
            false,
    		//'settings_section_callback',
    		$this->settings_ids['general']
    	);

        // Fields
    	add_settings_field(
    		'kk_kiyoh_connectorcode',
    		__( 'Connector code', 'kk_plugin' ),
    		array($this, 'render_kiyoh_connectorcode_field'),
    		$this->settings_ids['general'],
    		'kk_plugin_kiyoh_section'
    	);

    	add_settings_field(
    		'kk_kiyoh_company_id',
    		__( 'Company ID', 'kk_plugin' ),
    		array($this, 'render_kiyoh_company_id_field'),
    		$this->settings_ids['general'],
    		'kk_plugin_kiyoh_section'
    	);

    }

    /**
     * Add the KiyOh settings options
     *
     * @return void
     */


    private function register_kiyoh_com_settings()
    {

        // Sections

        add_settings_section(
    		'kk_plugin_kiyoh_com_section',
    		__( 'KiyOh.com settings', 'kk_plugin' ),
            false,
    		//'settings_section_callback',
    		$this->settings_ids['general']
    	);

        // Fields

    	add_settings_field(
    		'kk_kiyoh_com_connectorcode',
    		__( 'Connector code', 'kk_plugin' ),
    		array($this, 'render_kiyoh_com_connectorcode_field'),
    		$this->settings_ids['general'],
    		'kk_plugin_kiyoh_com_section'
    	);

    	add_settings_field(
    		'kk_kiyoh_com_company_id',
    		__( 'Company ID', 'kk_plugin' ),
    		array($this, 'render_kiyoh_com_company_id_field'),
    		$this->settings_ids['general'],
    		'kk_plugin_kiyoh_com_section'
    	);

    }

    /**
     * Klantenvertelling settings
     *
     *
     * @return void
     */

    private function register_klantenvertellen_settings()
    {

        // Sections

        add_settings_section(
            'kk_plugin_klantenvertellen_section',
            __( 'Klantenvertellen settings', 'kk_plugin' ),
            false,
        //	'settings_section_callback',
            $this->settings_ids['general']
        );

        // Fields
		
		

        add_settings_field(
            'kk_klantenvertellen_slug',
            __( 'My Klantenvertellen url', 'kk_plugin' ),
            array($this, 'render_klantenvertellen_slug_field'),
            $this->settings_ids['general'],
            'kk_plugin_klantenvertellen_section'
        );

        add_settings_field(
            'kk_klantenvertellen_company_name_field',
            __( 'Company name', 'kk_plugin' ),
            array($this, 'render_klantenvertellen_company_name_field'),
            $this->settings_ids['general'],
            'kk_plugin_klantenvertellen_section'
        );

        add_settings_field(
            'kk_klantenvertellen_name_field',
            __( 'Name field', 'kk_plugin' ),
            array($this, 'render_klantenvertellen_name_field'),
            $this->settings_ids['general'],
            'kk_plugin_klantenvertellen_section'
        );

        add_settings_field(
            'kk_klantenvertellen_review_text_field',
            __( 'Opinion field', 'kk_plugin' ),
            array($this, 'render_klantenvertellen_review_text_field'),
            $this->settings_ids['general'],
            'kk_plugin_klantenvertellen_section'
        );



    }

    /**
     * Klantenvertelling Mobiliteit settings
     *
     *
     * @since 1.0.2
     * @return void
     */

    private function register_klantenvertellen_mobiliteit_settings()
    {

        // Sections

        add_settings_section(
            'kk_plugin_klantenvertellen_mobiliteit_section',
            __( 'Klantenvertellen settings', 'kk_plugin' ),
            false,
        //	'settings_section_callback',
            $this->settings_ids['general']
        );

        // Fields

        add_settings_field(
            'kk_klantenvertellen_mobiliteit_slug',
            __( 'My Mobiliteit XML feed', 'kk_plugin' ),
            array($this, 'render_klantenvertellen_mobiliteit_slug_field'),
            $this->settings_ids['general'],
            'kk_plugin_klantenvertellen_mobiliteit_section'
        );
		add_settings_field(
            'kk_klantenvertellen_mobiliteit_name',
            __( 'Company name', 'kk_plugin' ),
            array($this, 'render_klantenvertellen_mobiliteit_name_field'),
            $this->settings_ids['general'],
            'kk_plugin_klantenvertellen_mobiliteit_section'
        );


    }

    /**
     * Klantenvertelling Mobiliteit settings
     *
     *
     * @since 1.0.2
     * @return void
     */

    private function register_klantenvertellen_v2_settings()
    {

        // Sections

        add_settings_section(
            'kk_plugin_klantenvertellen_v2_section',
            __( 'Klantenvertellen (V2) settings', 'kk_plugin' ),
            false,
        //	'settings_section_callback',
            $this->settings_ids['general']
        );

        // Fields
		//Deprecated since 1.1.4
		/*
        add_settings_field(
            'kk_klantenvertellen_v2_tenant_id',
            __( 'Tenant ID', 'kk_plugin' ),
            array($this, 'render_klantenvertellen_v2_tenant_id_field'),
            $this->settings_ids['general'],
            'kk_plugin_klantenvertellen_v2_section'
        );

        add_settings_field(
            'kk_klantenvertellen_v2_location_id',
            __( 'Location ID', 'kk_plugin' ),
            array($this, 'render_klantenvertellen_v2_location_id_field'),
            $this->settings_ids['general'],
            'kk_plugin_klantenvertellen_v2_section'
        );
		*/

		add_settings_field(
            'kk_klantenvertellen_v2_hashxml',
            __( 'XML-feed', 'kk_plugin' ),
            array($this, 'render_klantenvertellen_v2_hashxml_field'),
            $this->settings_ids['general'],
            'kk_plugin_klantenvertellen_v2_section'
        );
		
		add_settings_field(
            'kk_klantenvertellen_v2_company_url',
            __( 'URL to review page', 'kk_plugin' ),
            array($this, 'render_klantenvertellen_v2_company_url'),
            $this->settings_ids['general'],
            'kk_plugin_klantenvertellen_v2_section'
        );

    }

    /**
     * Render a debug setting field
     *
     * 
     * @return void
     */

    public function render_debug_settings_field()
    {
		$current_value = (isset($this->main_plugin->plugin_settings['default_settings']['debug']))?$this->main_plugin->plugin_settings['default_settings']['debug']:0;
		?>
		<fieldset>
			<input type="radio" name="<?php echo $this->settings_ids['default_settings']; ?>[debug]" value="0" <?php checked($current_value, 0); ?> id="kk_debug_0"><label for="kk_debug_0"><?php _e('Off','kk_plugin');?></label><br>
			<input type="radio" name="<?php echo $this->settings_ids['default_settings']; ?>[debug]" value="1" <?php checked($current_value, 1); ?> id="kk_debug_1"><label for="kk_debug_1"><?php _e('On','kk_plugin');?></label>
		</fieldset>
		<p class="description"><?php _e('When enabled plugin will check for new reviews every page request. This might slow down your website.','kk_plugin');?></p>
		<?php
	}
	
	/**
     * Render a debug setting field to show review ID
     *
     * 
     * @return void
     */

    public function render_debug_review_id_settings_field()
    {
		$current_value = (isset($this->main_plugin->plugin_settings['default_settings']['debugid']))?$this->main_plugin->plugin_settings['default_settings']['debugid']:0;
		?>
		<fieldset>
			<input type="radio" name="<?php echo $this->settings_ids['default_settings']; ?>[debugid]" value="0" <?php checked($current_value, 0); ?> id="kk_debug_0"><label for="kk_debug_0"><?php _e('Hide review IDs','kk_plugin');?></label><br>
			<input type="radio" name="<?php echo $this->settings_ids['default_settings']; ?>[debugid]" value="1" <?php checked($current_value, 1); ?> id="kk_debug_1"><label for="kk_debug_1"><?php _e('Show review IDs','kk_plugin');?></label>
		</fieldset>
		<p class="description"><?php _e('When enabled plugin will show review ID below each review.','kk_plugin');?></p>
		<?php
	}
	
	
    /**
     * Render a default setting field, based on widget setting
     *
     * @param array $field Field data
     * @return void
     */

    public function render_default_settings_field($field)
    {

        $field = $field['field'];
		if(isset($field['id'])) {
			$current_value = (isset($this->main_plugin->plugin_settings['default_settings'][$field['id']]))?$this->main_plugin->plugin_settings['default_settings'][$field['id']]:$field['default'];
		}

        switch ($field['type']) {
			case 'disabled':
				?>
				<strong><?php _e('Pro only','kk_plugin');?></strong>
				<br><span class="description"><?php echo $field['description']; ?></span><?php
				break;

            case 'select':
                ?>
                <select name="<?php echo $this->settings_ids['default_settings']; ?>[<?php echo $field['id']; ?>]">
                    <?php foreach ($field['options'] AS $value => $label):
                        ?>
                        <option value="<?php echo $value; ?>" <?php selected($current_value, $value); ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
				<?php if(isset($field['description']) && !empty($field['description'])) { ?>
				<br><span class="description"><?php echo $field['description']; ?></span>
				<?php }
                
                break;
            case 'checkbox':
                ?>
                <div>
                    <?php foreach ($field['options'] AS $value => $label):
                        $checked = '';
                        if (isset($current_value[$value]) && $current_value[$value] == 1) {
                            $checked = 'checked';
                        }
                        ?>
                        <div><input id="<?php echo $this->settings_ids['default_settings']; ?>_<?php echo $field['id']; ?>_<?php echo $value; ?>" type="checkbox" name="<?php echo $this->settings_ids['default_settings']; ?>[<?php echo $field['id']; ?>][<?php echo $value; ?>]" value="1" <?php echo $checked; ?> /><label for="<?php echo $this->settings_ids['default_settings']; ?>_<?php echo $field['id']; ?>_<?php echo $value; ?>"><?php echo $label; ?></label></div>
                    <?php endforeach; ?>
                </div>
                <?php
                break;
            case 'number':
                ?>
                <input type="number" name="<?php echo $this->settings_ids['default_settings']; ?>[<?php echo $field['id']; ?>]" value="<?php echo $current_value; ?>">
                <?php
                break;
			case 'textarea':
				?>
				<textarea class="textarea-custom-fields" name="<?php echo $this->settings_ids['default_settings']; ?>[<?php echo $field['id']; ?>]"><?php echo $current_value; ?></textarea>
				<span class="description"><?php echo $field['description']; ?></span>
				<?php 
				break;
            default:
                ?>
                <input type="text" name="<?php echo $this->settings_ids['default_settings']; ?>[<?php echo $field['id']; ?>]" value="<?php echo $current_value; ?>">
                <?php
        }

        if (isset($field['id']) && $field['id'] == 'start_with_review') {
            echo '<p class="description">'.__('All numbers of reviews before this number will be skipped.', 'kk_plugin').'</p>';
        } elseif (isset($field['id']) && $field['id'] == 'limit_review_length') {
            echo '<p class="description">'.__('Leave empty or use 0 to show the full text.', 'kk_plugin').'</p>';
        }


		

    }

    /**
     * Provider field (KiyOh or Klantenvertellen)
     *
     * @return void
     */

    public function render_provider_field() {
		?>
    	<select name="<?php echo $this->settings_ids['general']; ?>[kk_provider]">
			<option value="kiyoh_api" <?php selected( $this->main_plugin->plugin_settings['general']['kk_provider'], 'kiyoh_api' ); ?>>KiyOh</option>
			<option value="klantenvertellen_api" <?php selected( $this->main_plugin->plugin_settings['general']['kk_provider'], 'klantenvertellen_api' ); ?>>Klantenvertellen</option>
			<optgroup label="<?php _e('Old deprecated methods','kk_plugin');?>">
				<option value="kiyoh" <?php selected( $this->main_plugin->plugin_settings['general']['kk_provider'], 'kiyoh' ); ?>><?php _e('KiyOh.nl', 'kk_plugin'); ?></option>
				<option value="kiyoh_com" <?php selected( $this->main_plugin->plugin_settings['general']['kk_provider'], 'kiyoh_com' ); ?>><?php _e('KiyOh.com', 'kk_plugin'); ?></option>
				<option value="klantenvertellen" <?php selected( $this->main_plugin->plugin_settings['general']['kk_provider'], 'klantenvertellen' ); ?>><?php _e('Klantenvertellen', 'kk_plugin'); ?></option>
				<option value="klantenvertellen_mobiliteit" <?php selected( $this->main_plugin->plugin_settings['general']['kk_provider'], 'klantenvertellen_mobiliteit' ); ?>><?php _e('Klantenvertellen (Mobiliteit)', 'kk_plugin'); ?></option>
				<option value="klantenvertellen_v2" <?php selected( $this->main_plugin->plugin_settings['general']['kk_provider'], 'klantenvertellen_v2' ); ?>><?php _e('Klantenvertellen (V2)', 'kk_plugin'); ?></option>
			</optgroup>
    	</select>

        <p class="description"><?php _e('Choose if you\'re using KiyOh or Klantenvertellen', 'kk_plugin'); ?></p>
    <?php

    }

    /**
     * KiyOh connectorcode field
     *
     * @return void
     */

    public function render_kiyoh_connectorcode_field() {


    	?>
    	<input type="text" name='<?php echo $this->settings_ids['general']; ?>[kiyoh_connectorcode]' value='<?php echo (isset($this->main_plugin->plugin_settings['general']['kiyoh_connectorcode'])) ? $this->main_plugin->plugin_settings['general']['kiyoh_connectorcode'] : ''; ?>'>

        <p class="description"><?php echo sprintf(__('You can find the connector code on "your account" by logging in on %s', 'kk_plugin'),'<span class="kiyoh-domain"><a href="'.esc_url('https://kiyoh.nl/login.html').'" target="_blank">kiyoh.nl</a></span>'); ?></p>


        <?php

    }

    /**
     * KiyOh company id field
     *
     * @return void
     */

    public function render_kiyoh_company_id_field() {

    	?>
    	<input type="text" name='<?php echo $this->settings_ids['general']; ?>[kiyoh_company_id]' value='<?php echo (isset($this->main_plugin->plugin_settings['general']['kiyoh_company_id'])) ? $this->main_plugin->plugin_settings['general']['kiyoh_company_id'] : ''; ?>'>
		<p class="description"><?php echo sprintf(__('You can find the Company ID on "your account" by logging in on %s', 'kk_plugin'),'<span class="kiyoh-domain"><a href="'.esc_url('https://kiyoh.nl/login.html').'" target="_blank">kiyoh.nl</a></span>'); ?></p>

        <?php

    }

    /**
     * KiyOh connectorcode field
     *
     * @return void
     */

    public function render_kiyoh_com_connectorcode_field() {


    	?>
    	<input type="text" name='<?php echo $this->settings_ids['general']; ?>[kiyoh_com_connectorcode]' value='<?php echo (isset($this->main_plugin->plugin_settings['general']['kiyoh_com_connectorcode'])) ? $this->main_plugin->plugin_settings['general']['kiyoh_com_connectorcode'] : ''; ?>'>

        <p class="description"><?php echo sprintf(__('You can find the connector code on "your account" by logging in on %s', 'kk_plugin'),'<span class="kiyoh-domain"><a href="'.esc_url('https://kiyoh.com/login.html').'" target="_blank">kiyoh.com</a></span>'); ?></p>


        <?php

    }

    /**
     * KiyOh company id field
     *
     * @return void
     */

    public function render_kiyoh_com_company_id_field() {

    	?>
    	<input type="text" name='<?php echo $this->settings_ids['general']; ?>[kiyoh_com_company_id]' value='<?php echo (isset($this->main_plugin->plugin_settings['general']['kiyoh_com_company_id'])) ? $this->main_plugin->plugin_settings['general']['kiyoh_com_company_id'] : ''; ?>'>
		<p class="description"><?php echo sprintf(__('You can find the Company ID on "your account" by logging in on %s', 'kk_plugin'),'<span class="kiyoh-domain"><a href="'.esc_url('https://kiyoh.com/login.html').'" target="_blank">kiyoh.com</a></span>'); ?></p>

        <?php

    }

    /**
     * Klantenvertellen company slug field
     *
     * @return void
     */

     public function render_klantenvertellen_company_name_field() {


         $company_name = (isset($this->main_plugin->plugin_settings['general']['klantenvertellen_company_name']) && !empty($this->main_plugin->plugin_settings['general']['klantenvertellen_company_name'])) ? $this->main_plugin->plugin_settings['general']['klantenvertellen_company_name'] : get_bloginfo('name');

        ?>
        <input type="text" name='<?php echo $this->settings_ids['general']; ?>[klantenvertellen_company_name]' value='<?php echo $company_name; ?>'>

        <p class="description"><?php _e('Fill in your company name', 'kk_plugin'); ?></p>
        <?php

     }

     /**
      * Klantenvertellen company slug field
      *
      * @return void
      */

      public function render_klantenvertellen_slug_field() {

         ?>
         https://www.klantenvertellen.nl/referenties/<input type="text" name='<?php echo $this->settings_ids['general']; ?>[klantenvertellen_slug]' value='<?php if(isset($this->main_plugin->plugin_settings['general']['klantenvertellen_slug'])) echo $this->main_plugin->plugin_settings['general']['klantenvertellen_slug']; ?>'>

         <p class="description"><?php printf(__('For example: Is your personal klantenvertellen url %s? Then you fill in "%s"', 'kk_plugin'),'<code>https://www.klantenvertellen.nl/referenties/meer_succes_online</code>','<code>meer_succes_online</code>'); ?></p>
         <?php

      }

     /**
      * Klantenvertellen review text field
      *
      * @return void
      */

     public function render_klantenvertellen_review_text_field() {

         $attribute_names = $this->get_klantenvertellen_review_attributes();

         $check_value = (isset($this->main_plugin->plugin_settings['general']['klantenvertellen_review_text_field']))?$this->main_plugin->plugin_settings['general']['klantenvertellen_review_text_field']:null;

         if ($attribute_names !== false):
            ?>
            <select name="<?php echo $this->settings_ids['general']; ?>[klantenvertellen_review_text_field]">
				<option value=""><?php _e('No review text available','kk_plugin');?></option>
            <?php foreach($attribute_names AS $attribute_name): ?>
                <option <?php selected($check_value, $attribute_name); ?> value="<?php echo $attribute_name; ?>"><?php echo $attribute_name; ?></option>
            <?php endforeach; ?>

            </select>

            <p class="description"><?php printf(__('Choose the field that is used to show the contents of the review. This is usually "%s" or "%s".', 'kk_plugin'),'<code>Ervaring</code>','<code>Positieve ervaring</code>'); ?></p>

         <?php else: ?>

             <?php _e('No data available. Your account need to have reviews before this is available.', 'kk_plugin'); ?>

         <?php endif;

     }

     /**
      * Klantenvertellen name text field
      *
      * @return void
      */

     public function render_klantenvertellen_name_field() {

         $attribute_names = $this->get_klantenvertellen_review_attributes();

         $check_value = (isset($this->main_plugin->plugin_settings['general']['klantenvertellen_name_field']))?$this->main_plugin->plugin_settings['general']['klantenvertellen_name_field']:null;

         if ($attribute_names !== false):

            ?>
            <select name="<?php echo $this->settings_ids['general']; ?>[klantenvertellen_name_field]">
				<option value=""><?php _e('No name available / Anonymous','kk_plugin');?></option>
            <?php foreach($attribute_names AS $attribute_name): ?>
				<option <?php selected($check_value, $attribute_name); ?> value="<?php echo $attribute_name; ?>"><?php echo $attribute_name; ?></option>
            <?php endforeach; ?>

            </select>

            <p class="description"><?php printf(__('Choose the field that is used to show the (first) name of the reviewer. This is usually "%s".', 'kk_plugin'),'<code>Voornaam</code>'); ?></p>

         <?php else: ?>

             <?php _e('No data available. You need to have reviews before this is available.', 'kk_plugin'); ?>

         <?php endif;

     }

     /**
      * Get possible klantenvertellen attributes
      *
      * @return array|false Array of attribute, false if none available
      */

     public function get_klantenvertellen_review_attributes()
     {

         if ($this->klantenvertellen_review_attributes !== null)
            return $this->klantenvertellen_review_attributes;

         $data = $this->main_plugin->get_klantenvertellen_data();

         if ($data !== false && isset($data->resultaten->resultaat[0]) && count($data->resultaten->resultaat[0]->antwoord) > 0) {

             $attribute_names = array();

             foreach ($data->resultaten->resultaat[0]->antwoord AS $antwoord) {

                $attributes = $antwoord->attributes();

                $attribute_names[] = (string)$attributes['name'];

            }

            $this->klantenvertellen_review_attributes = $attribute_names;

            return $this->klantenvertellen_review_attributes;

        }

        return false;

     }

     /**
      * Klantenvertellen mobiliteit slug field
      *
      * @return void
      */

      public function render_klantenvertellen_mobiliteit_slug_field() {

         ?>
         https://mobiliteit.klantenvertellen.nl/xml/<input type="text" name='<?php echo $this->settings_ids['general']; ?>[klantenvertellen_mobiliteit_slug]' value='<?php if(isset($this->main_plugin->plugin_settings['general']['klantenvertellen_mobiliteit_slug'])) echo $this->main_plugin->plugin_settings['general']['klantenvertellen_mobiliteit_slug']; ?>'>

         <p class="description"><?php _e('You can find the link to your XML feed in your Klantenvertellen Mobiliteit account. If you cannot find this, you can contact your Klantenvertellen account manager.', 'kk_plugin'); ?></p>
         <?php

      }
	  /**
      * Klantenvertellen mobiliteit company name field
      *
      * @return void
      */

      public function render_klantenvertellen_mobiliteit_name_field() {

		 $company_name = (isset($this->main_plugin->plugin_settings['general']['klantenvertellen_mobiliteit_company_name']) && !empty($this->main_plugin->plugin_settings['general']['klantenvertellen_mobiliteit_company_name'])) ? $this->main_plugin->plugin_settings['general']['klantenvertellen_mobiliteit_company_name'] : get_bloginfo('name');
         ?>
		 <input type="text" name='<?php echo $this->settings_ids['general']; ?>[klantenvertellen_mobiliteit_company_name]' value='<?php echo $company_name; ?>'>

         <p class="description"><?php _e('Fill in your company name', 'kk_plugin'); ?></p>
         <?php

      }
	  
	  /**
       * Klantenvertellen V2 XML with hash
       *
       * @ since 1.1.4
       * @return void
      */
      public function render_klantenvertellen_v2_hashxml_field() {
          ?>
          <input type="text" class="regular-text" name='<?php echo $this->settings_ids['general']; ?>[klantenvertellen_v2_hashxml]' value='<?php if(isset($this->main_plugin->plugin_settings['general']['klantenvertellen_v2_hashxml'])) echo $this->main_plugin->plugin_settings['general']['klantenvertellen_v2_hashxml']; ?>'>
		  <p class="description"><?php printf(__('A Klantenvertellen XML-feed starts with %s.', 'kk_plugin'),'https://www.klantenvertellen.nl/v1/review/feed.xml?hash=<code>UNIEKE_CODE</code>'); ?></p>
          <p class="description" style="margin-top:30px;"><?php printf(__('You can find the XML-feed on "your account" under "Publication options" by logging in on %s.', 'kk_plugin'),'<a href="'.esc_url('https://www.klantenvertellen.nl').'" target="_blank">klantenvertellen.nl</a>'); ?></p>
		  <p class="description"><?php printf(__("Can't find your XML-feed? Contact your %s.","kk_plugin"),'<a href="https://klantenvertellen.nl/contact/" target="_blank">'.__('Klantenvertellen accountmanager','kk_plugin').'</a>'); ?></p>
          <?php
      }
	  
	  
	  /**
       * Klantenvertellen V2 Company URL
       *
       * @ since 1.1.5
       * @return void
      */
		public function render_klantenvertellen_v2_company_url() {
          ?>
          <input type="text" class="regular-text" name='<?php echo $this->settings_ids['general']; ?>[klantenvertellen_v2_company_url]' value='<?php if(isset($this->main_plugin->plugin_settings['general']['klantenvertellen_v2_company_url'])) echo $this->main_plugin->plugin_settings['general']['klantenvertellen_v2_company_url']; ?>' placeholder="https://www.klantenvertellen.nl/reviews/...">
		  <p class="description"><?php _e('Enter complete URL to your Klantenvertellen review page.', 'kk_plugin'); ?></p>
          <p class="description"><?php printf(__('URL to review page starts with %s.', 'kk_plugin'),'https://www.klantenvertellen.nl/reviews/<code>ID</code>'); ?></p>

          <?php

      }
	  

      /**
       * Email subject field
       *
       * @return void
       */

       public function render_email_enabled_field() {

          ?>
          <input id="emails_enabled" type="checkbox" name='<?php echo $this->settings_ids['emails']; ?>[emails_enabled]' value='1' <?php if (isset($this->main_plugin->plugin_settings['emails']['emails_enabled'])) checked($this->main_plugin->plugin_settings['emails']['emails_enabled'] ,1); ?>> <label for="emails_enabled"><?php _e('Enable', 'kk_plugin'); ?></label>

          <p class="description"><?php echo sprintf(__('Enable an automatic invite e-mail which will be send after a WooCommerce order has been completed.', 'kk_plugin'),'<a href="'.esc_url('https://www.klantenvertellen.nl').'" target="_blank">klantenvertellen.nl</a>'); ?></p>

          <?php

       }

      /**
       * Email subject field
       *
       * @return void
       */

       public function render_email_subject_field() {

          ?>
          <input type="text" name='<?php echo $this->settings_ids['emails']; ?>[emails_subject]' value='<?php echo (isset($this->main_plugin->plugin_settings['emails']['emails_subject'])) ? $this->main_plugin->plugin_settings['emails']['emails_subject'] : ''; ?>'>
          <?php

       }

       /**
        * Email body field
        *
        * @return void
        */

        public function render_email_body_field() {

			$provider = $this->main_plugin->plugin_settings['general']['kk_provider'];
			$data=$this->main_plugin->get_data($provider);
			$createReviewUrl=$data['createReviewUrl'];
			$easyInviteLink=str_replace('add-review','invite-link',$createReviewUrl).'?lang='.substr( get_locale(), 0, 2 );
           ?>
           <textarea name='<?php echo $this->settings_ids['emails']; ?>[emails_body]'><?php echo (isset($this->main_plugin->plugin_settings['emails']['emails_body'])) ? $this->main_plugin->plugin_settings['emails']['emails_body'] : ''; ?></textarea>
		   <p class="description"><?php _e("Don't forget to insert your Easy Invite link inside the mail.","kk_plugin");?></p>
		   <p class="description"><?php printf(__('You can use the following tags to replace with order data: %s = billing full name, %s = billing first name, %s = billing last name, %s = billing email, %s = billing city, %s = order date, %s = order completed date.', 'kk_plugin'),'<code>{{name}}</code>','<code>{{first_name}}</code>','<code>{{last_name}}</code>','<code>{{email}}</code>','<code>{{city}}</code>','<code>{{order_date}}</code>','<code>{{completed_date}}</code>'); ?></p>
		   <p class="description"><?php printf(__('Use %s inside your Easy Invite link add all product ID from your order to your review.', 'kk_plugin'),'<code>{{product_ids}}</code>'); ?></p>
		   <p class="description"><?php printf(__('Example Easy Invite link: %s.','kk_plugin'),'<code>'.$easyInviteLink.'&email={{email}}&name={{first_name}}&city={{city}}&{{product_ids}}</code>');?></p>
           <?php
        }

        /**
         * Email send after field
         *
         * @return void
         */

         public function render_email_send_after_field() {

            ?>
            <input type="number" name='<?php echo $this->settings_ids['emails']; ?>[emails_send_after]' value='<?php echo (isset($this->main_plugin->plugin_settings['emails']['emails_send_after'])) ? $this->main_plugin->plugin_settings['emails']['emails_send_after'] : ''; ?>'> <?php _e('day(s)', 'kk_plugin'); ?> <?php _e('after an order has been marked as completed.', 'kk_plugin'); ?>
            <p class="description"><?php echo sprintf(__('Leave empty or enter 0 to send immediately after an order has been marked as completed. The e-mail queue will be processed twice a day.', 'kk_plugin'),'<a href="'.esc_url('https://www.klantenvertellen.nl').'" target="_blank">klantenvertellen.nl</a>'); ?></p>

            <?php

         }

         public function render_email_send_to_roles_field() {

             ?>
             <div class="kk-single-checkbox">
                    <input id="email_send_to_wc_guest" type="checkbox" name="<?php echo $this->settings_ids['emails']; ?>[emails_send_to_roles][wc_guest]" <?php if (isset($this->main_plugin->plugin_settings['emails']['emails_send_to_roles']) && in_array('wc_guest', $this->main_plugin->plugin_settings['emails']['emails_send_to_roles'])) echo 'CHECKED'; ?> value="wc_guest"> <label for="email_send_to_wc_guest"><?php _e('Guest', 'kk_plugin'); ?></label>
             </div>
            <?php foreach (get_editable_roles() as $role_name => $role_info): ?>
                <div class="kk-single-checkbox">
                    <input id="email_send_to_<?php echo $role_name; ?>" type="checkbox" name="<?php echo $this->settings_ids['emails']; ?>[emails_send_to_roles][<?php echo $role_name; ?>]" <?php if (isset($this->main_plugin->plugin_settings['emails']['emails_send_to_roles']) && in_array($role_name, $this->main_plugin->plugin_settings['emails']['emails_send_to_roles'])) echo 'CHECKED'; ?> value="<?php echo $role_name; ?>"> <label for="email_send_to_<?php echo $role_name; ?>"><?php echo $role_info['name']; ?></label>
                </div>

            <?php endforeach; ?>
            <p class="description"><?php echo sprintf(__('Only send e-mails to guests or customers with a specific role.', 'kk_plugin'),'<a href="'.esc_url('https://www.klantenvertellen.nl').'" target="_blank">klantenvertellen.nl</a>'); ?></p>

            <?php

         }

     /**
      * Render the options page
      *
      * @return void
      */

    public function render_options_page() {

        $current_action = (isset($_GET['action']) && !empty($_GET['action']))?$_GET['action']:'general';				
		
        $transientTimestamp = get_transient( 'kk_plugin_connection_check' );

        if ($transientTimestamp < 86400 || $transientTimestamp == false) {
            $transientTimestamp = current_time( 'timestamp' );
        }

		if ( false === ( $connection_time = sprintf(_x('Last check: %s ago','%s = human-readable time difference','kk_plugin'),'<em>'.human_time_diff($transientTimestamp, current_time( 'timestamp' )).'</em>') ) ) {
			$connection_time=false;		
		}

		
        if( $this->main_plugin->get_debug_status() ) { 
            ?>
            <div class="notice notice-warning is-dismissible">
                <p><?php _e( 'Warning! Debugging mode is turned on. This may cause unexpected problems.','kk_plugin'); ?></p>
            </div>
            <?php
        }

        include(KK_PLUGIN_VIEWS_PATH.'/admin/settings.php');

    }
	
	
	/**
     * Klantenvertelling API settings
     *
     *
     * @return void
     */

    private function register_klantenvertellen_api_settings()
    {

        // Sections
		add_settings_section(
            'kk_plugin_klantenvertellen_api_section',
            __( 'Klantenvertellen settings', 'kk_plugin' ),
            false,
        //	'settings_section_callback',
            $this->settings_ids['general']
        );

        // Fields
        add_settings_field(
            'kk_klantenvertellen_api_token',
            __( 'Klantenvertellen API Token', 'kk_plugin' ),
            array($this, 'render_klantenvertellen_api_token_field'),
            $this->settings_ids['general'],
            'kk_plugin_klantenvertellen_api_section'
        );
		
		// Fields
        add_settings_field(
            'kk_klantenvertellen_api_locationId',
            __( 'Klantenvertellen Location ID', 'kk_plugin' ),
            array($this, 'render_klantenvertellen_api_locationId_field'),
            $this->settings_ids['general'],
            'kk_plugin_klantenvertellen_api_section'
        );
		
		/*add_settings_field(
            'kk_klantenvertellen_api_tenantId',
            __( 'Klantenvertellen Tenant ID', 'kk_plugin' ),
            array($this, 'render_klantenvertellen_api_tenantId_field'),
            $this->settings_ids['general'],
            'kk_plugin_klantenvertellen_api_section'
        );*/

        if(get_option('kk_plugin_backup_klantenvertellen')) {
			// Fields
			add_settings_field(
				'kk_klantenvertellen_api_backup',
				__( 'Show backup data', 'kk_plugin' ),
				array($this, 'render_klantenvertellen_api_backup_field'),
				$this->settings_ids['general'],
				'kk_plugin_klantenvertellen_api_section'
			);
		}
		
		add_settings_field(
			'kk_plugin_refresh_time',
			__( 'Refresh rate', 'kk_plugin' ),
			array($this, 'render_refresh_rate_field'),
			$this->settings_ids['general'],
			'kk_plugin_klantenvertellen_api_section'
		);
		

    }
	
	
	/**
      * Klantenvertellen API Token field
      *
      * @return void
      */

    public function render_klantenvertellen_api_token_field() {

         ?>
         <input type="text" class="large-text" name='<?php echo $this->settings_ids['general']; ?>[klantenvertellen_api_token]' value='<?php if(isset($this->main_plugin->plugin_settings['general']['klantenvertellen_api_token'])) echo $this->main_plugin->plugin_settings['general']['klantenvertellen_api_token']; ?>'>

         <p class="description"><?php printf(__('Contact %s and ask your %s accountmanager for your API Token.', 'kk_plugin'),'Klantenvertellen','Klantenvertellen'); ?></p>
         <?php

    }
	
	/**
      * Klantenvertellen API LocationID field
      *
      * @return void
      */

    public function render_klantenvertellen_api_locationId_field() {

         ?>
         <input type="text" name='<?php echo $this->settings_ids['general']; ?>[klantenvertellen_api_locationId]' value='<?php if(isset($this->main_plugin->plugin_settings['general']['klantenvertellen_api_locationId'])) echo $this->main_plugin->plugin_settings['general']['klantenvertellen_api_locationId']; ?>'>

         <p class="description"><?php printf(__('The Location ID is the number combination that you also see at the invite link or review page in the URL.', 'kk_plugin'),'Klantenvertellen','Klantenvertellen'); ?></p>
         <?php
		 /* Documentation 
		 <p><img src="<?php echo KK_PLUGIN_DIR_URL;?>/assets/images/kk-plugin-invite-link.png" alt="<?php _e('Example Invite link','kk_plugin');?>"></p>
		 */
    }
	
	/**
      * Klantenvertellen API tenantID field
      *
      * @return void
      

    public function render_klantenvertellen_api_tenantId_field() {

         ?>
         <input type="text" name='<?php echo $this->settings_ids['general']; ?>[klantenvertellen_api_tenantId]' value='<?php if(isset($this->main_plugin->plugin_settings['general']['klantenvertellen_api_tenantId'])) echo $this->main_plugin->plugin_settings['general']['klantenvertellen_api_tenantId']; ?>'>

         <p class="description"><?php printf(__('Optional. Ask your %s accountmanager for more information.', 'kk_plugin'),'Klantenvertellen'); ?></p>
         <?php

    }*/
	
	/**
      * Klantenvertellen API Backup field
      *
      * @return void
      */
    public function render_klantenvertellen_api_backup_field() {
         ?>
		 <p class="kk-plugin-disabled"><input type="checkbox" name="" value="1" disabled id="show_old_data_kk"><label for="show_old_data_kk"><?php printf(__('Old %s backup exists. Show old review data below new review data.','kk_plugin'),'Klantenvertellen');?> </label> <strong><?php _e('(PRO only)','kk_plugin');?></strong></p>
         <?php
    }
	
	
	/**
     * KiyOh API settings
     *
     *
     * @return void
     */

    private function register_kiyoh_api_settings()
    {

        // Sections
		add_settings_section(
            'kk_plugin_kiyoh_api_section',
            __( 'KiyOh settings', 'kk_plugin' ),
            false,
        //	'settings_section_callback',
            $this->settings_ids['general']
        );

        // Fields
        add_settings_field(
            'kk_kiyoh_api_token',
            __( 'KiyOh API Token', 'kk_plugin' ),
            array($this, 'render_kiyoh_api_token_field'),
            $this->settings_ids['general'],
            'kk_plugin_kiyoh_api_section'
        );
		
		// Fields
        add_settings_field(
            'kk_kiyoh_api_locationId',
            __( 'KiyOh Location ID', 'kk_plugin' ),
            array($this, 'render_kiyoh_api_locationId_field'),
            $this->settings_ids['general'],
            'kk_plugin_kiyoh_api_section'
        );
		
		/*add_settings_field(
            'kk_kiyoh_api_tenantId',
            __( 'KiyOh Tenant ID', 'kk_plugin' ),
            array($this, 'render_kiyoh_api_tenantId_field'),
            $this->settings_ids['general'],
            'kk_plugin_kiyoh_api_section'
        );*/

        if(get_option('kk_plugin_backup_kiyoh')) {
			// Fields
			add_settings_field(
				'kk_kiyoh_api_backup',
				__( 'Show backup data', 'kk_plugin' ),
				array($this, 'render_kiyoh_api_backup_field'),
				$this->settings_ids['general'],
				'kk_plugin_kiyoh_api_section'
			);
		}
		
		add_settings_field(
			'kk_plugin_refresh_time',
			__( 'Refresh rate', 'kk_plugin' ),
			array($this, 'render_refresh_rate_field'),
			$this->settings_ids['general'],
			'kk_plugin_klantenvertellen_api_section'
		);
		

    }
	
	
	/**
      * KiyOh API Token field
      *
      * @return void
      */

    public function render_kiyoh_api_token_field() {

         ?>
         <input type="text" class="large-text" name='<?php echo $this->settings_ids['general']; ?>[kiyoh_api_token]' value='<?php if(isset($this->main_plugin->plugin_settings['general']['kiyoh_api_token'])) echo $this->main_plugin->plugin_settings['general']['kiyoh_api_token']; ?>'>

         <p class="description"><?php printf(__('Contact %s and ask your %s accountmanager for your API Token.', 'kk_plugin'),'KiyOh','KiyOh'); ?></p>
         <?php

    }
	
	/**
      * KiyOh API LocationID field
      *
      * @return void
      */

    public function render_kiyoh_api_locationId_field() {

         ?>
         <input type="text" name='<?php echo $this->settings_ids['general']; ?>[kiyoh_api_locationId]' value='<?php if(isset($this->main_plugin->plugin_settings['general']['kiyoh_api_locationId'])) echo $this->main_plugin->plugin_settings['general']['kiyoh_api_locationId']; ?>'>

         <p class="description"><?php printf(__('The Location ID is the number combination that you also see at the invite link or review page in the URL.', 'kk_plugin'),'KiyOh','KiyOh'); ?></p>
         <?php

    }
	
	/**
      * KiyOh API tenantID field
      *
      * @return void
      

    public function render_kiyoh_api_tenantId_field() {

         ?>
         <input type="text" name='<?php echo $this->settings_ids['general']; ?>[kiyoh_api_tenantId]' value='<?php if(isset($this->main_plugin->plugin_settings['general']['kiyoh_api_tenantId'])) echo $this->main_plugin->plugin_settings['general']['kiyoh_api_tenantId']; ?>'>

         <p class="description"><?php printf(__('Optional. Ask your %s accountmanager for more information.', 'kk_plugin'),'KiyOh'); ?></p>
         <?php

    }
	
	*/
	
	/**
      * KiyOh API Backup field
      *
      * @return void
      */
    public function render_kiyoh_api_backup_field() {
         ?>
		 <p class="kk-plugin-disabled"><input type="checkbox" disabled value="1" id="show_old_data_kk"><label for="show_old_data_kk"><?php printf(__('Old %s backup exists. Show old review data below new review data.','kk_plugin'),'KiyOh');?> </label> <strong><?php _e('(PRO only)','kk_plugin');?></strong></p>
         <?php
    }
	
	public function translateType($type) {
		switch($type) {
			case 'INT':
				$output = __('Rating','kk_plugin');
				break;
			case 'TEXT':
				$output = __('Text','kk_plugin');
				break;
			case 'BOOLEAN':
				$output = __('Yes or No','kk_plugin');
				break;
			case 'IMAGE':
				$output = __('Image','kk_plugin');
				break;
			case 'SELECT_LIST':
			case 'SELECT':
				$output = __('Selection','kk_plugin');
				break;
			default:
				$output = __('Unknown','kk_plugin');
		}
		return $output;
	}
	
	/**
      * Refresh rate field
      *
      * @return void
      */

    public function render_refresh_rate_field() {
		?>
		<p class="kk-plugin-disabled">
			<input class="small-text" disabled id="kk_plugin_refresh_rate" type="number" min="1" max="1000" value="24"> <?php _e('hours','kk_plugin');?> <strong><?php _e('(PRO only)','kk_plugin');?></strong>
		</p>
		<p class="kk-plugin-disabled description"><span><?php printf(__('Refresh reviews each %d hours.', 'kk_plugin'),'24'); ?> <?php _e('Lower refresh rates (< 6 hours) can lead to connection problems. We advise to set refresh rate as high as possible.','kk_plugin');?></span> </p>
		<?php
    }
	
	
}
?>
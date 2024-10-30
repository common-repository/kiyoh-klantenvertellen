<?php
/**
* Adds KiyOh / Klantenvertellen widget
*/

namespace KiyOh_Klantenvertellen;

class API extends \WP_Widget {

    /**
    * Widget Fields
    */
    private $widget_fields;

    private $main_plugin;

	/**
	* Register widget with WordPress
	*/
	public function __construct($main_plugin) {
		parent::__construct(
			'kiyohklantenvertell_widget', // Base ID
			esc_html__( 'KiyOh / Klantenvertellen API', 'kk_plugin' ), // Name
			array( 'description' => esc_html__( 'Display KiyOh / Klantenvertellen (version 2.0)', 'kk_plugin' ), ) // Args
		);

        $this->main_plugin = $main_plugin;

        $this->widget_fields = static::get_widget_fields();

        add_action( 'admin_enqueue_scripts', array($this, 'enqueue_widget_scripts') );
	}

    /**
     * Get widget fields
     *
     * @return array Array of widget fields
     */

    public static function get_widget_fields()
    {
        return array(
			/*array(
                'label' => __('Custom review data', 'kk_plugin'),
                'id' => 'custom_review_data',
                'default' => '',
                'type' => 'textarea',
				'description'=>sprintf(__('Only available for Klantenvertellen version 2 users. Use HTML and placeholders to show custom review data. See %s for more information.','kk_plugin'),'<a href="https://wordpress.org/plugins/kiyoh-klantenvertellen/#installation">'.__('documentation','kk_plugin').'</a>'),
				'section'=>'single_review_settings',
            ),*/

        );
    }

    /**
     * Enqueue widget scripts
     *
     * @param string $hook Hook name
     */

    public function enqueue_widget_scripts($hook)
    {
        if ($hook == 'widgets.php') {
            wp_enqueue_script( KK_PLUGIN_ID.'_admin', KK_PLUGIN_DIR_URL . 'assets/admin/widget.js', array('jquery') );
        }
    }

	/**
	* Front-end display of widget
	*/
	public function widget( $args, $instance ) {


		echo $args['before_widget'];

		// Output widget title
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

        // Defaults
        $widget_fields = self::get_widget_fields();

        foreach ($widget_fields AS $widget_field) {

            if (!isset($instance[$widget_field['id']])) {
                $instance[$widget_field['id']] = $widget_field['default'];
            }

        }

        echo $this->main_plugin->get_layout($instance['layout'], array(
            'show_summary' => $instance['show_summary'],
            'show_logo' => $instance['show_logo'],
            'logo_type' => $instance['logo_type'],
            'do_show_reviews' => $instance['do_show_reviews'],
			'show_date' => $instance['show_date'],
			'show_average_stars' => $instance['show_average_stars'],
			'show_stars' => $instance['show_stars'],
            'stars_theme' => $instance['stars_theme'],
            'stars_size' => $instance['stars_size'],
            'show_reviews_amount' => $instance['show_reviews_amount'],
            'limit_review_length' => $instance['limit_review_length'],
            'start_with_review' => $instance['start_with_review'],
            'show_review_rating' => $instance['show_review_rating'],
			'show_average_rating' => $instance['show_average_rating'],
            'review_display_options' => $instance['review_display_options'],
            'auto_slide' => $instance['auto_slide'],
        ));


		echo $args['after_widget'];
	}

	/**
	* Back-end widget fields
	*/
	public function field_generator( $instance ) {

        $widget_fields = $this->widget_fields;

        if (isset($this->main_plugin->plugin_settings['default_settings']) && !empty($this->main_plugin->plugin_settings['default_settings']) && empty($instance)) {

            $instance = $this->main_plugin->plugin_settings['default_settings'];
        }

        include(KK_PLUGIN_VIEWS_PATH.'/admin/widget-fields.php');

	}

	public function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'kk_plugin' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'kk_plugin' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
		$this->field_generator( $instance );
	}

	/**
	* Sanitize widget form values as they are saved
	*/
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		foreach ( $this->widget_fields as $widget_field ) {
			switch ( $widget_field['type'] ) {
				case 'checkbox':
					$instance[$widget_field['id']] = $new_instance[$widget_field['id']]; // Array
					break;
				default:
					$instance[$widget_field['id']] = ( ! empty( $new_instance[$widget_field['id']] ) ) ? strip_tags( $new_instance[$widget_field['id']] ) : '';
			}
		}

		return $instance;
	}
}
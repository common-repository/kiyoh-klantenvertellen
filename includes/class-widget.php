<?php
/**
* Adds KiyOh / Klantenvertellen widget
*/

namespace KiyOh_Klantenvertellen;

class Widget extends \WP_Widget {

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
			esc_html__( 'KiyOh / Klantenvertellen', 'kk_plugin' ) .' '.esc_html__( '(Deprecated)', 'kk_plugin' ), // Name
			array( 'description' => esc_html__( 'Display KiyOh / Klantenvertellen', 'kk_plugin' ).' '.esc_html__( 'This widget is deprecated. We advise to use shortcodes instead.', 'kk_plugin' ), ) // Args
		);

        $this->main_plugin = $main_plugin;
        $this->widget_fields = static::get_widget_fields();

        add_action('admin_enqueue_scripts', array($this, 'enqueue_widget_scripts'));
	}

    /**
     * Get widget fields
     *
     * @return array Array of widget fields
     */

    public static function get_widget_fields()
    {
        $fields=array(
            array(
                'label' => __('Layout', 'kk_plugin'),
                'id' => 'layout',
                'type' => 'select',
                'default' => 'default',
                'options' => array(
                    'default' => __('Default', 'kk_plugin'),
                    'slider' => __('Slider', 'kk_plugin'),
					'list' => __('List', 'kk_plugin'),
                )
            ),
            array(
                'label' => __('Auto slide', 'kk_plugin'),
                'id' => 'auto_slide',
                'type' => 'select',
                'default' => 'no',
                'options' => array(
                    'no' => __('No', 'kk_plugin'),
                    'yes' => __('Yes', 'kk_plugin'),
                )
            ),
            /**
             * @since 1.0.4
             */
            array(
                'label' => __('Review display options', 'kk_plugin'),
                'id' => 'review_display_options',
                'type' => 'checkbox',
                'default' => null,
                'options' => array(
                    'hide_without_rating' => __('Hide reviews without rating', 'kk_plugin'),
                    'hide_without_rating_text' => __('Hide reviews without review text', 'kk_plugin'),
                    'hide_without_author' => __('Hide reviews without author', 'kk_plugin'),
                )
            ),
            array(
                'label' => __('Show average stars', 'kk_plugin'),
                'id' => 'show_average_stars',
                'type' => 'select',
                'default' => 'yes',
                'options' => array(
                    'yes' => __('Yes', 'kk_plugin'),
                    'no' => __('No', 'kk_plugin')
                )
            ),
            array(
                'label' => __('Star theme', 'kk_plugin'),
                'id' => 'stars_theme',
                'type' => 'select',
                'default' => 'default',
                'options' => array(
                    'default' => __('Yellow + gray', 'kk_plugin'),
                    'yellow' => __('Yellow', 'kk_plugin'),
                    'black-grey' => __('Black + gray', 'kk_plugin'),
                    'black' => __('Black', 'kk_plugin'),
                    'white' => __('White', 'kk_plugin'),
                )
            ),
            array(
                'label' => __('Stars size', 'kk_plugin'),
                'id' => 'stars_size',
                'type' => 'select',
                'default' => 'small',
                'options' => array(
                    'small' => __('Small', 'kk_plugin'),
                    'medium' => __('Medium', 'kk_plugin'),
                    'large' => __('Large', 'kk_plugin'),
                )
            ),
            array(
                'label' => __('Show logo', 'kk_plugin'),
                'id' => 'show_logo',
                'type' => 'select',
                'default' => 'yes',
                'options' => array(
                    'yes' => __('Yes', 'kk_plugin'),
                    'no' => __('No', 'kk_plugin')
                )
            ),
            array(
                'label' => __('Logo type', 'kk_plugin'),
                'id' => 'logo_type',
                'type' => 'select',
                'default' => 'image',
                'options' => array(
                    'image' => __('Image only', 'kk_plugin'),
                    'full' => __('Full logo', 'kk_plugin'),
                )
            ),
            array(
                'label' => __('Show summary', 'kk_plugin'),
                'id' => 'show_summary',
                'type' => 'select',
                'default' => 'yes',
                'options' => array(
                    'yes' => __('Yes', 'kk_plugin'),
                    'no' => __('No', 'kk_plugin')
                )
            ),
            array(
                'label' => __('Show date', 'kk_plugin'),
                'id' => 'show_date',
                'type' => 'select',
                'default' => 'no',
                'options' => array(
                    'yes' => __('Yes', 'kk_plugin'),
                    'no' => __('No', 'kk_plugin')
                )
            ),
            array(
                'label' => __('Show reviews', 'kk_plugin'),
                'id' => 'do_show_reviews',
                'type' => 'select',
                'default' => 'yes',
                'options' => array(
                    'yes' => __('Yes', 'kk_plugin'),
                    'no' => __('No', 'kk_plugin')
                )
            ),
            array(
                'label' => __('Show x reviews', 'kk_plugin'),
                'id' => 'show_reviews_amount',
                'default' => '1',
                'type' => 'number',
            ),
            array(
                'label' => __('Start with review x', 'kk_plugin'),
                'id' => 'start_with_review',
                'default' => '0',
                'type' => 'number',
            ),

            array(
                'label' => __('Limit review length at x words', 'kk_plugin'),
                'id' => 'limit_review_length',
                'default' => '0',
                'type' => 'number',
            ),
            
			array(
                'label' => __('Show average score', 'kk_plugin'),
                'id' => 'show_average_rating',
                'default' => 'no',
                'type' => 'select',
                'options' => array(
                    'yes' => __('Yes', 'kk_plugin'),
                    'no' => __('No', 'kk_plugin')
                )
            ),
			array(
					'label' => __('Show single review score', 'kk_plugin'),
					'id' => 'show_review_rating',
					'default' => 'yes',
					'type' => 'select',
					'options' => array(
						'yes' => __('Yes', 'kk_plugin'),
						'no' => __('No', 'kk_plugin')
					)
				),
				array(
					'label' => __('Show single review stars', 'kk_plugin'),
					'id' => 'show_stars',
					'type' => 'select',
					'default' => 'no',
					'options' => array(
						'yes' => __('Yes', 'kk_plugin'),
						'no' => __('No', 'kk_plugin')
					)
				)
			);

		return $fields;
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
			'class' => '',
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
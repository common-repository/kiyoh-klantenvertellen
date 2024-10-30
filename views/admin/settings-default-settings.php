<?php
use KiyOh_Klantenvertellen\KiyOh_Klantenvertellen_Plugin;
?>

<form action="options.php" method="post">

    <?php settings_fields($this->settings_ids['default_settings']); ?>

    <div id="kk-plugin-default-settings" class="kk-plugin-settings-box">

        <div class="kk-plugin-default-settings-form">

            <h2><?php _e('Layout settings', 'kk_plugin'); ?></h2>
            <table class="form-table">
				<?php do_settings_fields( $this->settings_ids['default_settings'], 'kk_plugin_default_settings_section' ); ?>
            </table>
			<?php
				$provider = $this->main_plugin->plugin_settings['general']['kk_provider'];
				if($provider=='klantenvertellen_api' || $provider=='kiyoh_api') {
			?>
			<h2><?php _e('Custom review questions', 'kk_plugin'); ?></h2>
			<p><?php printf(__("Show custom review questions and answers with our %s.","kk_plugin"),'<a href="https://wpfortune.com/shop/plugins/kiyoh-klantenvertellen/">'.__('Premium plugin','kk_plugin').'</a>');?></p>
            <table class="form-table">
				<?php do_settings_fields( $this->settings_ids['default_settings'], 'kk_plugin_single_review_settings_section' ); ?>
            </table>
				<?php } ?>
			<h2><?php _e('Debug settings','kk_plugin');?></h2>
			<table class="form-table">
				<?php do_settings_fields( $this->settings_ids['default_settings'], 'kk_plugin_debug_settings_section' ); ?>
			</table>
        </div>

        <div class="kk-plugin-default-settings-example">
            <h3><?php _e('Example', 'kk_plugin'); ?></h3>
            <p><?php _e('The ID as shown for each review is not visible on the frontend.', 'kk_plugin'); ?></p>
            <?php echo do_shortcode('[kiyoh-klantenvertellen show_id="yes"]'); ?>

        </div>

        <div class="kk-clearfix"></div>

    </div>

    <?php submit_button(); ?>

</form>
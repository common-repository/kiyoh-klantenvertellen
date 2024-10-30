<?php use \KiyOh_Klantenvertellen\KiyOh_Klantenvertellen_Plugin; ?>

<div class="wrap">

    <div class="kk-plugin-main-settings">

        <h1><?php _e('KiyOh / Klantenvertellen', 'kk_plugin');?></h1>
    	<p><?php _e('Show reviews from KiyOh or Klantenvertellen.nl with a shortcode.', 'kk_plugin');?></p>
        <h2 class="nav-tab-wrapper">
            <a href="<?php echo admin_url( 'options-general.php?page='.KK_PLUGIN_ID ); ?>" class="nav-tab<?php echo ($current_action == 'general')?' nav-tab-active':''; ?>"><?php esc_html_e( 'General', 'kk_plugin' ); ?></a>
            <a href="<?php echo esc_url( add_query_arg( array( 'action' => 'default_settings' ), admin_url( 'options-general.php?page='.KK_PLUGIN_ID ) ) ); ?>" class="nav-tab<?php echo ($current_action == 'default_settings')?' nav-tab-active':''; ?>"><?php esc_html_e( 'Default settings', 'kk_plugin' ); ?></a>
            <?php if (KiyOh_Klantenvertellen_Plugin::is_woocommerce_activated()): ?>
                <a href="<?php echo esc_url( add_query_arg( array( 'action' => 'emails' ), admin_url( 'options-general.php?page='.KK_PLUGIN_ID ) ) ); ?>" class="nav-tab<?php echo ($current_action == 'emails')?' nav-tab-active':''; ?>"><?php esc_html_e( 'WooCommerce invites', 'kk_plugin' ); ?></a>
            <?php endif; ?>
            <a href="<?php echo esc_url( add_query_arg( array( 'action' => 'help' ), admin_url( 'options-general.php?page='.KK_PLUGIN_ID ) ) ); ?>" class="nav-tab<?php echo ($current_action == 'help')?' nav-tab-active':''; ?>"><?php esc_html_e( 'Help', 'kk_plugin' ); ?></a>
        </h2>

        <?php if ($current_action == 'general'): ?>
            <?php include(KK_PLUGIN_VIEWS_PATH.'/admin/settings-general.php'); ?>
        <?php elseif ($current_action == 'default_settings'): ?>
            <?php include(KK_PLUGIN_VIEWS_PATH.'/admin/settings-default-settings.php'); ?>
        <?php elseif ($current_action == 'emails' && KiyOh_Klantenvertellen_Plugin::is_woocommerce_activated()): ?>
            <?php include(KK_PLUGIN_VIEWS_PATH.'/admin/settings-emails.php'); ?>
        <?php elseif ($current_action == 'help'): ?>
            <?php include(KK_PLUGIN_VIEWS_PATH.'/admin/settings-help.php'); ?>
        <?php endif; ?>

    </div>

    <div class="kk-plugin-sidebar">

        <div class="kk-single-sidebar-box box-1">
            <p><?php printf(__('This plugin is brought to you by %s', 'kk_plugin'), '<a href="https://wordpress.org/plugins/kiyoh-klantenvertellen/">PEP</a>'); ?></p>
			<h3><?php _e('Need support?','kk_plugin');?></h3>
			<p><?php printf(__('Need support with this plugin? Use the %s and we will reply within 7 days.','kk_plugin'),'<a href="https://wordpress.org/plugins/kiyoh-klantenvertellen/" target="_blank">'.__('WordPress.org forums','kk_plugin').'</a>');?></p>
			<h3><?php _e('Get priority support!','kk_plugin');?></h3>
			<p><?php printf(__('Or checkout our %s and get support by priority support by mail within 2 workdays.','kk_plugin'),'<a href="https://wpfortune.com/shop/plugins/kiyoh-klantenvertellen/" target="_blank">'.__('Premium plugin','kk_plugin').'</a>');?></p>
			<h3><?php _e('No support by phone','kk_plugin');?></h3>
			<p><?php printf(__("You may always call us for new projects, but unfortunately we %s","kk_plugin"),'<strong>'.__('cannot provide support by phone.','kk_plugin').'</strong>');?> <?php _e("Please don't call us for plugin related questions.","kk_plugin");?></p>
        </div>

        <div class="kk-single-sidebar-box disclaimer">
            <?php printf(__('KiyOh / Klantenvertellen plugin will save review data from your KiyOh or Klantenvertellen account. This data could contain personal data of your reviewers. Data is stored in %s on your own website. This data is not shared with the developer of this plugin or with others.', 'kk_plugin'), '<a href="https://codex.wordpress.org/Options_API" target="_blank">'.__('WordPress options table','kk_plugin').'</a>'); ?>
        </div>

    </div>

    <div class="kk-clearfix"></div>

</div>
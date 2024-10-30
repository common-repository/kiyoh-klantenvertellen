<?php
/**
 * @param $this Settings_Page 
 */
use KiyOh_Klantenvertellen\KiyOh_Klantenvertellen_Plugin;?>

	
	
<form action="<?php echo remove_query_arg(array('transient-cleared'), 'options.php'); ?>" method="post">    
	<?php 	
	settings_fields($this->settings_ids['general']); 
	
	$provider=$this->main_plugin->plugin_settings['general']['kk_provider'];
	
	if($provider!="klantenvertellen_api" && $provider!="kiyoh_api") { ?>
	<div class="notice notice-error"><p><?php _e('You use an outdated method to collect customer reviews.','kk_plugin');?> <?php _e('Contact your KiyOh / Klantenvertellen account manager for new API data.','kk_plugin');?> <?php _e('Then choose the new API method for KiyOh or Klantenvertellen below.','kk_plugin');?></p></div>
	<?php
	}
	?>

    <div id="kk-plugin-common-settings" class="kk-plugin-settings-box">
		
        <h2><?php _e('General', 'kk_plugin'); ?></h2>
        <table class="form-table">
            <?php do_settings_fields( $this->settings_ids['general'], 'kk_plugin_common_section' ); ?>
        </table>

    </div>
	
	
	<?php if($provider=='kiyoh_api') { ?>
	<div id="kk-plugin-kiyoh-api-settings" class="kk-plugin-settings-box kk-plugin-settings-box-show-hide">

        <h2><?php _e('KiyOh settings', 'kk_plugin'); ?></h2>

        <?php if($this->main_plugin->check_data_connection($provider)): ?>
                <div class="kk-success"><?php _e('Connection with KiyOh: OK.', 'kk_plugin'); ?> <?php if($connection_time) echo $connection_time;?></div>
        <?php else: ?>
                <div class="kk-notice"><?php _e('Cannot connect to KiyOh, please check your settings or try again later.', 'kk_plugin'); ?></div>
        <?php endif; ?>

        <table class="form-table">
            <?php do_settings_fields( $this->settings_ids['general'], 'kk_plugin_kiyoh_api_section' ); ?>
			
			<?php if(get_option('kk_plugin_backup_kiyoh')) {
				
			}?>
			
        </table>
    </div>
	<?php } ?>
	
	<?php if($provider=='klantenvertellen_api') { ?>
	<div id="kk-plugin-klantenvertellen-api-settings" class="kk-plugin-settings-box kk-plugin-settings-box-show-hide">

        <h2><?php _e('Klantenvertellen settings', 'kk_plugin'); ?></h2>

        <?php if($this->main_plugin->check_data_connection($provider)){ ?>
                <div class="kk-success"><?php _e('Connection with Klantenvertellen: OK.', 'kk_plugin'); ?> <?php if($connection_time) echo $connection_time;?></div>
        <?php } else { ?>
                <div class="kk-notice"><?php _e('Cannot connect to Klantenvertellen, please check your settings or try again later.', 'kk_plugin'); ?></div>
        <?php }  ?>

        <table class="form-table">
            <?php do_settings_fields( $this->settings_ids['general'], 'kk_plugin_klantenvertellen_api_section' ); ?>
        </table>
    </div>
	
	<?php } ?>
	
	
	<?php // Old deprecated methods 
	if($provider=='kiyoh') { 
	?>
    <div id="kk-plugin-kiyoh-settings" class="kk-plugin-settings-box kk-plugin-settings-box-show-hide">

        <h2><?php _e('KiyOh settings', 'kk_plugin'); ?></h2>

        <?php if($this->main_plugin->check_data_connection($provider)): ?>
                <div class="kk-success"><?php _e('Connection with KiyOh: OK.', 'kk_plugin'); ?> <?php if($connection_time) echo $connection_time;?></div>
        <?php else: ?>
                <div class="kk-notice"><?php _e('Cannot connect to KiyOh, please check your settings or try again later.', 'kk_plugin'); ?></div>
        <?php endif; ?>

        <table class="form-table">
            <?php do_settings_fields( $this->settings_ids['general'], 'kk_plugin_kiyoh_section' ); ?>
        </table>
		
		<div id="kk-plugin-backup-xml">
			<h3><?php _e('Back-up old feed','kk_plugin');?></h3>
			<p><?php printf(__('From May 2019, %s has started migrating all customer reviews to a new platform.','kk_plugin'),'KiyOh');?></p>
			<p><?php printf(__('%s will stop using the old XML method from mid-September.','kk_plugin'),'KiyOh');?></p>
			<?php if(get_option('kk_plugin_backup_kiyoh')) {
				?><p><strong><?php _e('OK: A backup of all customer reviews already exists.','kk_plugin');?> <?php _e('You may now switch to the new API method to receive your new customer reviews.','kk_plugin');?></strong></p>
			<?php
			} ?>
			
		</div>

    </div>
	<?php } ?>
	
	<?php if($provider=='kiyoh_com') { ?>
    <div id="kk-plugin-kiyoh-com-settings" class="kk-plugin-settings-box kk-plugin-settings-box-show-hide">

        <h2><?php _e('KiyOh.com settings', 'kk_plugin'); ?></h2>

        <?php if($this->main_plugin->check_data_connection($provider)): ?>
                <div class="kk-success"><?php _e('Connection with KiyOh.com: OK.', 'kk_plugin'); ?> <?php if($connection_time) echo $connection_time;?></div>
        <?php else: ?>
                <div class="kk-notice"><?php _e('Cannot connect to KiyOh.com, please check your settings or try again later.', 'kk_plugin'); ?></div>
        <?php endif; ?>

        <table class="form-table">
            <?php do_settings_fields( $this->settings_ids['general'], 'kk_plugin_kiyoh_com_section' ); ?>
        </table>
		
		<div id="kk-plugin-backup-xml">
			<h3><?php _e('Back-up old feed','kk_plugin');?></h3>
			<p><?php printf(__('From May 2019, %s has started migrating all customer reviews to a new platform.','kk_plugin'),'KiyOh');?></p>
			<p><?php printf(__('%s will stop using the old XML method from mid-September.','kk_plugin'),'KiyOh');?></p>
			<?php if(get_option('kk_plugin_backup_kiyoh_com')) {
				?><p><strong><?php _e('OK: A backup of all customer reviews already exists.','kk_plugin');?> <?php _e('You may now switch to the new API method to receive your new customer reviews.','kk_plugin');?></strong></p>
			<?php
			} ?>
			
		</div>

    </div>
	<?php } ?>
	
	<?php if($provider=='klantenvertellen') { ?>
    <div id="kk-plugin-klantenvertellen-settings"  class="kk-plugin-settings-box kk-plugin-settings-box-show-hide">

        <h2><?php _e('Klantenvertellen settings', 'kk_plugin'); ?></h2>

        <?php if($this->main_plugin->check_data_connection($provider)): ?>
            <div class="kk-success"><?php _e('Connection with Klantenvertellen: OK.', 'kk_plugin'); ?> <?php if($connection_time) echo $connection_time;?></div>
        <?php else: ?>
            <div class="kk-notice"><?php _e('Cannot connect to Klantenvertellen, please check your settings or try again later.', 'kk_plugin'); ?></div>
        <?php endif; ?>

        <table class="form-table">
            <?php do_settings_fields( $this->settings_ids['general'], 'kk_plugin_klantenvertellen_section' ); ?>
        </table>
		
		<div id="kk-plugin-backup-xml">
			<h3><?php _e('Back-up old feed','kk_plugin');?></h3>
			<p><?php printf(__('From May 2019, %s has started migrating all customer reviews to a new platform.','kk_plugin'),'Klantenvertellen');?></p>
			<p><?php printf(__('%s will stop using the old XML method from mid-September.','kk_plugin'),'Klantenvertellen');?></p>
			<?php if(get_option('kk_plugin_backup_klantenvertellen')) {
				?><p><strong><?php _e('OK: A backup of all customer reviews already exists.','kk_plugin');?> <?php _e('You may now switch to the new API method to receive your new customer reviews.','kk_plugin');?></strong></p>
				<?php
			}?>
			
		</div>

    </div>
	<?php } ?>
	
	<?php if($provider=='klantenvertellen_mobiliteit') { ?>
    <div id="kk-plugin-klantenvertellen-mobiliteit-settings"  class="kk-plugin-settings-box kk-plugin-settings-box-show-hide">

        <h2><?php _e('Klantenvertellen (Mobiliteit) settings', 'kk_plugin'); ?></h2>

        <?php if($this->main_plugin->check_data_connection($provider)): ?>
            <div class="kk-success"><?php _e('Connection with Klantenvertellen (Mobiliteit): OK.', 'kk_plugin'); ?> <?php if($connection_time) echo $connection_time;?></div>
        <?php else: ?>
            <div class="kk-notice"><?php _e('Cannot connect to Klantenvertellen (Mobiliteit), please check your settings or try again later.', 'kk_plugin'); ?></div>
        <?php endif; ?>

        <table class="form-table">
            <?php do_settings_fields( $this->settings_ids['general'], 'kk_plugin_klantenvertellen_mobiliteit_section' ); ?>
        </table>
		
		<div id="kk-plugin-backup-xml">
			<h3><?php _e('Back-up old feed','kk_plugin');?></h3>
			<p><?php printf(__('From May 2019, %s has started migrating all customer reviews to a new platform.','kk_plugin'),'Klantenvertellen');?></p>
			<p><?php printf(__('%s will stop using the old XML method from mid-September.','kk_plugin'),'Klantenvertellen');?></p>
			<?php if(get_option('kk_plugin_backup_klantenvertellen_mobiliteit')) {
				?><p><strong><?php _e('OK: A backup of all customer reviews already exists.','kk_plugin');?> <?php _e('You may now switch to the new API method to receive your new customer reviews.','kk_plugin');?></strong></p>
			<?php
			} ?>
			
		</div>

    </div>
	<?php } ?>

	<?php if($provider=='klantenvertellen_v2') { ?>
    <div id="kk-plugin-klantenvertellen-v2-settings"  class="kk-plugin-settings-box kk-plugin-settings-box-show-hide">

        <h2><?php _e('Klantenvertellen (V2) settings', 'kk_plugin'); ?></h2>

        <?php if($this->main_plugin->check_data_connection($provider)): ?>
            <div class="kk-success"><?php _e('Connection with Klantenvertellen (V2): OK.', 'kk_plugin'); ?> <?php if($connection_time) echo $connection_time;?></div>
        <?php else: ?>
            <div class="kk-notice"><?php _e('Cannot connect to Klantenvertellen (V2), please check your XML-feed.', 'kk_plugin'); ?></div>
        <?php endif; ?>

        <table class="form-table">
            <?php do_settings_fields( $this->settings_ids['general'], 'kk_plugin_klantenvertellen_v2_section' ); ?>
        </table>

    </div>
	<?php } ?>

    <?php submit_button(); ?>

</form>

<div id="kk-plugin-clear-transient">

    <a href="<?php echo esc_url( add_query_arg( array( 'clear-transient' => 'true' ), admin_url( 'options-general.php?page='.KK_PLUGIN_ID ) ) ); ?>" class="button-secondary"><?php _e('Refresh results / clear cache', 'kk_plugin'); ?></a>
    <div class="kk-plugin-clear-transient-description"><?php _e('Click this button to refresh the review results manually. Review results will be automatically refreshed once a day.', 'kk_plugin'); ?></div>

    <div class="kk-clearfix"></div>

</div>
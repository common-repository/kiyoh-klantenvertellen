<?php
/*
Plugin Name: KiyOh / Klantenvertellen
Plugin URI: https://wordpress.org/plugins/kiyoh-klantenvertellen/
Description: Toon reviews in Google rich snippet formaat van KiyOh of Klantenvertellen met shortcodes.
Version: 2.0.12
Author: PEP
Author URI: https://pepbc.nl/
Text Domain: kk_plugin
License: GPLv2
*/

if ( ! defined( 'ABSPATH' ) )
	die();

if(!defined('KK_PLUGIN_VERSION')) {
	define( 'KK_PLUGIN_VERSION', '2.0.12' );
	define( 'KK_PLUGIN_NAME', 'KiyOh / Klantenvertellen' );
	define( 'KK_PLUGIN_ID', 'kiyoh_klantenvertellen' );
	define( 'KK_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
	define( 'KK_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
	define( 'KK_PLUGIN_VIEWS_PATH', KK_PLUGIN_DIR_PATH.'views');
	define( 'KK_PLUGIN_FILE', plugin_basename(__FILE__));
}

// Class autoloader
spl_autoload_register('kiyoh_klantenvertellen_autoloader');

register_activation_hook(__FILE__, function(){
    if (! wp_next_scheduled ( 'cron_send_order_invite_emails' )) {
         wp_schedule_event(time(), 'twicedaily', 'cron_send_order_invite_emails');
    }
    $emails_option = get_option('kk_plugin_settings_emails');
    if (empty($emails_option)) {
        update_option('kk_plugin_settings_emails', array(
            'emails_subject' => __('Rate us!', 'kk_plugin'),
            'emails_body' => __("Dear customer,\n\nPlease give us a rating if you're pleased with our products and/or services.\n\nClick the link below to write a review:\nAdd your link here", "kk_plugin"),
            'emails_send_to_roles' => array(
                'wc_guest' => 'wc_guest',
                'administrator' => 'administrator',
                'customer' => 'customer',
            )
        ));
    }
});
register_deactivation_hook(__FILE__, function(){
    wp_clear_scheduled_hook('cron_send_order_invite_emails');
});
// Init plugin
add_action( 'plugins_loaded', 'kiyoh_klantenvertellen_init' );

function kiyoh_klantenvertellen_init() {

    $kiyoh_klantenvertellen = new \KiyOh_Klantenvertellen\KiyOh_Klantenvertellen_Plugin();
    $kk_settings_page = new \KiyOh_Klantenvertellen\Settings_Page( $kiyoh_klantenvertellen );
    $kk_shortcode = new \KiyOh_Klantenvertellen\Shortcode( $kiyoh_klantenvertellen );

}

/**
 * Class autoloader
 *
 * @param string $class Class name
 */

function kiyoh_klantenvertellen_autoloader($class)
{
    // project-specific namespace prefix
    $prefix = 'KiyOh_Klantenvertellen\\';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_dirs_class = substr($class, $len);

    // Directories
    $directory = '';
    $dirs = explode('\\', $relative_dirs_class);
    $relative_class = array_pop($dirs);
    if (is_array($dirs) && count($dirs)) {
        $directory = implode('/ ', $dirs).'/';
    }
    $file = KK_PLUGIN_DIR_PATH .'/includes/'.$directory.'class-'.strtolower(str_replace('_', '-', $relative_class)). '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }

}
?>
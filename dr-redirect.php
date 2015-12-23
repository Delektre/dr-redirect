<?php
/**
 * Plugin Name: Delektre Redirect
 * Plugin URI: http://www.delektre.fi
 * Description: A brief description about your plugin.
 * Version: 1.0.0
 * Author: Tommi Rintala
 * Author URI: http://www.delektre.fi/tommi.rintala
 * License: GPLv2
 * Domain Path: /dr-redirect
 */

defined('ABSPATH') or die('No script kiddies please!');

define('DR_VERSION', '1.0.0');
define('DR_DIR', plugin_dir_path(__FILE__). '/dr-redirect');
define('DR_URL', plugins_url(__FILE__) . '/dr-redirect');

    

function dr_plugin_install() {
    
}

function dr_plugin_uninstall() {
    
}

function dr_db_install() {
    global $wpdb;

    $table_name = $wpdb->prefix . "_dredir";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      time datetime DEFAULT NOW(),
      src_url text NOT NULL,
      src_title text NOT NULL,
      dst_url text NOT NULL,
      enabled int DEFAULT 0
     ) $charset_collate;";

    require_once(ABSPATH . "wp-admin/includes/upgrade.php");
    dbDelta( $sql );

    update_option('dr_db_version', DR_VERSION);
}

function dr_update_db_check() {
    if ( get_site_option('dr_db_version') != DR_VERSION) {
        dr_db_install();
    }
}

add_action('plugins_loaded', 'dr_update_db_check');

function dr_register() {
}

function dr_activation() {
}

function dr_insert_rule() {
    global $wpdb;

    $table_name = $wpdb->prefix . "_dredir";

    $wpdb->insert(
        $table_name,
        array(
            'src_url' => '',
            'dst_url' => '',
            'src_title' => '',
            'enabled' => 0
        ),
        array(
            '%s', '%s', '%s', '%d'
        )
    );
}

function dr_update_rule($ruleid, $src_url, $src_title, $dst_url, $enabled) {
    global $wpdb;

    $table_name = $wpdb->prefix . "_dredir";

    $wpdb->insert(
        $table_name,
        array(
            'src_url' => $src_url,
            'dst_url' => $dst_url,
            'src_title' => $src_title,
            'enabled' => $enabled
        ),
        array('id' => $ruleid ),
        array('%s', '%s', '%s', '%d')
    );
}

function dr_delete_rule($ruleid) {
    global $wpdb;

    $table_name = $wpdb->prefix . "_dredir";

    $wpdb->delete(
        $table_name,
        array('id' => $ruleid)
    );
        
}

function dr_create_menu() {
    // create top-level menu
    add_menu_page('DR Redirect', 'DR Redirect', 'administrator', __FILE__,
    'dr_settings_page', plugins_url('/images/icon.png', __FILE__));
    // call register settings function
    add_action('admin_init', 'dr_register_settings');
}

function dr_register_settings() {
    register_setting('dr_redirect_site', 'sitename');
}

function dr_settings_page() {
?>
    <div class="wrap">
    <h2>Redirect settings</h2>
    <form method="post" action="dr-redirect.php">
    <table class="wp-list-table widefat fixed striped posts">
      <tr valign="top">
    <th>Enabled</th>
    <th>Src URL</th>
    <th>Src Title</th>
    <th>Dst Page</th>
      </tr>
    </table>
    <?php submit_button ?>
    </form>
    <?php
}

if (is_admin()) {   
    add_action('init', 'dr_register');
    // register_activation_hook(__FILE__, 'dr_activation');
    add_action('admin_menu', 'dr_create_menu');
}



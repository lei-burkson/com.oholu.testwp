<?php
/*
Plugin Name: WPS Locator
Description:
Version:
Author:
Author
*/
// function to create the DB / Options / Defaults
function wps_options_install() {

    global $wpdb;

    $table_name = $wpdb->prefix . "wps_locations";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `LocationNumber` varchar(32) NOT NULL,
            `LocationName` varchar(255) NOT NULL,
            `LocationAddress` varchar(255) NOT NULL,
            `LocationAddress2` varchar(128) NULL,
            `LocationCity` varchar(128) NOT NULL,
            `LocationState` varchar(128) NOT NULL,
            `LocationZip` varchar(16) NOT NULL,
            `LocationPhone` varchar(32) NOT NULL,
            `LocationCountry` varchar(128) NULL,
            `LocationLatitude` decimal(10, 8) NOT NULL,
            `LocationLongitude` decimal(11, 8) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY (`LocationNumber`)
          ) $charset_collate; ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'wps_options_install');

//menu items
add_action('admin_menu','wps_locator_modifymenu');
function wps_locator_modifymenu() {

	//this is the main item for the menu
	add_menu_page('WPS Locator', //page title
	'Locations', //menu title
	'manage_options', //capabilities
	'wps_locator_list', //menu slug
	'wps_locator_list' //function
	);

	//this is a submenu
	add_submenu_page('wps_locator_list', //parent slug
	'Add New Location', //page title
	'Add New', //menu title
	'manage_options', //capability
	'wps_locator_create', //menu slug
	'wps_locator_create'); //function

	//this is a submenu
	add_submenu_page('wps_locator_list', //parent slug
	'Import Locations', //page title
	'Import', //menu title
	'manage_options', //capability
	'wps_locator_import', //menu slug
	'wps_locator_import'); //function

	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
	'Update Location', //page title
	'Update', //menu title
	'manage_options', //capability
	'wps_locator_update', //menu slug
	'wps_locator_update'); //function

	//this is a submenu
	add_submenu_page('wps_locator_list', //parent slug
	'WPS Locator Shortcodes', //page title
	'Shortcodes', //menu title
	'manage_options', //capability
	'wps_locator_shortcodes', //menu slug
	'wps_locator_shortcodes'); //function
}

define('ROOTDIR', plugin_dir_path(__FILE__));
require_once(ROOTDIR . 'includes/locations-list.php');
require_once(ROOTDIR . 'includes/locations-create.php');
require_once(ROOTDIR . 'includes/locations-update.php');
require_once(ROOTDIR . 'includes/locations-import.php');
require_once(ROOTDIR . 'includes/locations-export.php');
require_once(ROOTDIR . 'includes/locations-shortcodes.php');
require_once(ROOTDIR . 'includes/locations-ajax.php');
require_once(ROOTDIR . 'wps-shortcode-utils.php');
require_once(ROOTDIR . 'wps-shortcode-map.php');

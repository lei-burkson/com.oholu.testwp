<?php
/*
Plugin Name: WPS Subscriber
Description:
Version:
Author:
Author
*/
// function to create the DB / Options / Defaults
function wps_subscriber_options_install() {

    global $wpdb;

    $table_name = $wpdb->prefix . "wps_subscribers";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `SubscriberEmail` varchar(320) NOT NULL,
            `SubscriberName` varchar(255) NULL,
            `SubscriberAddress` varchar(255) NULL,
            `SubscriberAddress2` varchar(128) NULL,
            `SubscriberCity` varchar(128) NULL,
            `SubscriberState` varchar(128) NULL,
            `SubscriberZip` varchar(16) NULL,
            `SubscriberPhone` varchar(32) NULL,
            `SubscriberCountry` varchar(128) NULL,
            `SubscriberUnsubscribed` varchar(6) NOT NULL,
			`SubscriberToken` varchar(36) NOT NULL,
			`SubscriberCreated` timestamp NULL DEFAULT NULL,
			`SubscriberLastUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY (`SubscriberEmail`)
          ) $charset_collate; ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'wps_options_install');

//menu items
add_action('admin_menu','wps_subscriber_modifymenu');
function wps_subscriber_modifymenu() {

	//this is the main item for the menu
	add_menu_page('WPS Subscriber', //page title
	'Subscribers', //menu title
	'manage_options', //capabilities
	'wps_subscriber_list', //menu slug
	'wps_subscriber_list' //function
	);

	//this is a submenu
	add_submenu_page('wps_subscriber_list', //parent slug
	'Export Subscribers', //page title
	'Export', //menu title
	'manage_options', //capability
	'wps_subscriber_export', //menu slug
	'wps_subscriber_export'); //function

	//this is a submenu
	add_submenu_page('wps_subscriber_list', //parent slug
	'WPS Subscriber Shortcodes', //page title
	'Shortcodes', //menu title
	'manage_options', //capability
	'wps_subscriber_shortcodes', //menu slug
	'wps_subscriber_shortcodes'); //function
}

define('ROOTDIR', plugin_dir_path(__FILE__));
require_once(ROOTDIR . 'includes/subscribers-list.php');
require_once(ROOTDIR . 'includes/subscribers-export.php');
require_once(ROOTDIR . 'includes/subscribers-shortcodes.php');
require_once(ROOTDIR . 'includes/subscribers-ajax.php');
require_once(ROOTDIR . 'wps-shortcode-subs.php');

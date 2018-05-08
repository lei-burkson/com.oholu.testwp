<?php
/*
Plugin Name: WPS Gallery
Description:
Version:
Author:
Author
*/
// function to create the DB / Options / Defaults
function wps_options_install() {
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'wps_options_install');

//menu items
add_action('admin_menu','wps_gallery_modifymenu');
function wps_gallery_modifymenu() {

	//this is the main item for the menu
	add_menu_page('WPS Gallery', //page title
	'Gallery', //menu title
	'manage_options', //capabilities
	'wps_gallery_main', //menu slug
	'wps_gallery_main' //function
	);
}

define('ROOTDIR', plugin_dir_path(__FILE__));
require_once(ROOTDIR . 'includes/gallery-main.php');
require_once(ROOTDIR . 'includes/gallery-ajax.php');

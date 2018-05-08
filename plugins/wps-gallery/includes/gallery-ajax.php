<?php
/**
 * Add Gallery Filter
 */

// The function that handles the AJAX request
function wps_get_gallery_objects () {
	check_ajax_referer( 'wps-gallery-nonce', 'security' );

	// Get requested starting lat and lng
	$slug = $_GET['slug'];
	$cnt = empty($_GET['cnt']) ? 10 : intval($_GET['cnt'], 10);

	if (empty($slug) || !$cnt) {
		echo json_encode(array('error' => 'Missing or invalid parameters.'));
		die();
	}

	$outPosts = array();

	foreach ($rows as $key => $post) {
	}

	if ( empty( $outPosts ) ) {
		echo json_encode(array('error' => 'No results returned.'));
		die();
	}

	echo json_encode($outPosts);

	die(); // this is required to return a proper result
}
add_action( 'wp_ajax_wps_get_gallery_objects', 'wps_get_gallery_objects' );
add_action( 'wp_ajax_nopriv_wps_get_gallery_objects', 'wps_get_gallery_objects' );

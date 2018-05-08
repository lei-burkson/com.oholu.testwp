<?php

function add_script_attribute($tag, $handle) {
    if ( 'gmap' !== $handle )
        return $tag;
    return str_replace( ' src', ' async defer src', $tag );
}

add_filter('script_loader_tag', 'add_script_attribute', 10, 2);

function wps_shortcode_map($atts = []) {
	// Save $atts.
	$atts = shortcode_atts( array(
		'api_key'  => '',
		'loader_image' => plugins_url('wps-locator/frontend/icons/grid-blue.svg')
	), $atts );

	wp_enqueue_style( 'wps', plugins_url( 'wps-locator/frontend/css/main.css' ) );
	wp_enqueue_script( 'wps', plugins_url( 'wps-locator/frontend/js/locator.js' ), array('jquery'), '1.0.0', true);
	wp_enqueue_script( 'gmap', 'https://maps.googleapis.com/maps/api/js?key='.$atts['api_key'].'&libraries=geometry&callback=initWPSMap', array(), NULL, true);
	wp_localize_script( 'wps', 'wpsLocator', array(
		// URL to wp-admin/admin-ajax.php to process the request
		'ajaxurl' => admin_url( 'admin-ajax.php' ),

		// generate a nonce with a unique ID "wps-locator-nonce"
		// so that you can check it later when an AJAX request is sent
		'security' => wp_create_nonce( 'wps-locator-nonce' )
	));
    ?>
        <div class="wps-locator-map-wrap" id="wps-locator-map-wrap" data-loader="<?php echo $atts['loader_image']; ?>">
        </div>
    <?php
}
add_shortcode( 'wps_locator_map', 'wps_shortcode_map' );
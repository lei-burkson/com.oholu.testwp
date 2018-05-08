<?php

function add_script_attribute($tag, $handle) {
    if ( 'gmap' !== $handle )
        return $tag;
    return str_replace( ' src', ' async defer src', $tag );
}

add_filter('script_loader_tag', 'add_script_attribute', 10, 2);

function wps_shortcode_subs($attrs = []) {
	// Save $atts.
	$_atts = shortcode_atts( array(
    ), $atts );

	wp_enqueue_style( 'wps', plugins_url( 'wps-subscriber/frontend/css/main.css' ) );
	wp_enqueue_script( 'wps', plugins_url( 'wps-subscriber/frontend/js/subscriber.js' ), array('jquery'), '1.0.0', true);
	wp_localize_script( 'wps', 'wpsSubscribers', array(
		// URL to wp-admin/admin-ajax.php to process the request
		'ajaxurl' => admin_url( 'admin-ajax.php' ),

		// generate a nonce with a unique ID "wps-subscriber-nonce"
		// so that you can check it later when an AJAX request is sent
		'security' => wp_create_nonce( 'wps-subscriber-nonce' )
	));

    ?>
        <form class="wps-subscriber-wrap" autocomplete="off">
            <div class="wps-input-group">
                <span class="wps-input-group-addon"><img src="<?=plugins_url('wps-locator/frontend/icons/search.svg')?>"/></span>
                <input type="text" class="wps-form-control" autocomplete="off" placeholder="ENTER Email" name="email" aria-label="Subscribe">
                <a href="javascript:void(0);" class="wps-input-group-addon wps-subscribe-btn">Subscribe</a>
            </div>
        </form>
    <?php
}
add_shortcode( 'wps_subscriber_utils', 'wps_shortcode_subs' );
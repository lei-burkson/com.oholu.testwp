<?php

function wps_shortcode_utils($attrs = []) {
	// Save $atts.
	$_atts = shortcode_atts( array(
		'api_key'  => ''
	), $atts );

    ?>
        <div class="wps-locator-utils-wrap">
            <form class="wps-locator-search-wrap" autocomplete="off">
                <div class="wps-input-group">
                    <span class="wps-input-group-addon"><img src="<?=plugins_url('wps-locator/frontend/icons/search.svg')?>"/></span>
                    <input type="text" class="wps-form-control" autocomplete="off" placeholder="ENTER ZIP" name="location" aria-label="Seach Location">
                    <a href="javascript:void(0);" class="wps-input-group-addon wps-use-location-btn"><i class="wps-use-location-icon"></i></a>
                </div>
            </form>
            <div class="wps-locator-scroll-wrap">
                <div class="wps-locator-result-list">
                </div>
            </div>
        </div>
    <?php
}
add_shortcode( 'wps_locator_utils', 'wps_shortcode_utils' );
<?php

function wps_locator_shortcodes() {
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/wps-locator/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Shortcodes</h2>
        <p class="description">There are two shortcodes available, both of which are required to form a complete locator. Arrange them freely on your page.</p>
        <h3>Map</h3>
        <div><pre class="wps-code-pre">[wps_locator_map api_key="{your_google_map_api_key}" loader_image="{your_loader_image_url}"]</pre></div>
        <h3>Search Box and List</h3>
        <div><pre class="wps-code-pre">[wps_locator_utils]</pre></div>
    </div>
    <?php
}

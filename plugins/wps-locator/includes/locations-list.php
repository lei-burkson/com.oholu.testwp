<?php

function wps_locator_list() {
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/wps-locator/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Locations</h2>
        <p class="actions">
            <a href="<?php echo admin_url('admin.php?page=wps_locator_create'); ?>">Add New</a>
        </p>
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . "wps_locations";

        $rows = $wpdb->get_results("SELECT id, LocationNumber, LocationName, LocationAddress, LocationAddress2, LocationCity,
            LocationState, LocationZip, LocationPhone, LocationCountry, LocationLatitude, LocationLongitude from $table_name");
        ?>
        <div class="responsive-table">
	        <table class='widefat fixed striped posts'>
	            <tr>
	                <th class="manage-column wps-list-width">ID</th>
	                <th class="manage-column wps-list-width">Number</th>
	                <th class="manage-column wps-list-width">Name</th>
	                <th class="manage-column wps-list-width">Address</th>
	                <th class="manage-column wps-list-width">Address2</th>
	                <th class="manage-column wps-list-width">City</th>
	                <th class="manage-column wps-list-width">State</th>
	                <th class="manage-column wps-list-width">Zip</th>
	                <!--<th class="manage-column wps-list-width">Location Phone</th>
	                <th class="manage-column wps-list-width">Location Country</th>
	                <th class="manage-column wps-list-width">Location Latitude</th>
	                <th class="manage-column wps-list-width">Location Longitude</th>-->
	                <th>&nbsp;</th>
	            </tr>
	            <?php foreach ($rows as $row) { ?>
	                <tr class="">
	                    <td class="manage-column wps-list-width"><?php echo $row->id; ?></td>
	                    <td class="manage-column wps-list-width"><?php echo $row->LocationNumber; ?></td>
	                    <td class="manage-column wps-list-width"><?php echo $row->LocationName; ?></td>
	                    <td class="manage-column wps-list-width"><?php echo $row->LocationAddress; ?></td>
	                    <td class="manage-column wps-list-width"><?php echo $row->LocationAddress2; ?></td>
	                    <td class="manage-column wps-list-width"><?php echo $row->LocationCity; ?></td>
	                    <td class="manage-column wps-list-width"><?php echo $row->LocationState; ?></td>
	                    <td class="manage-column wps-list-width"><?php echo $row->LocationZip; ?></td>
	                    <!--<td class="manage-column wps-list-width"><?php echo $row->LocationPhone; ?></td>
	                    <td class="manage-column wps-list-width"><?php echo $row->LocationCountry; ?></td>
	                    <td class="manage-column wps-list-width"><?php echo $row->LocationLatitude; ?></td>
	                    <td class="manage-column wps-list-width"><?php echo $row->LocationLongitude; ?></td>-->
	                    <td><a href="<?php echo admin_url('admin.php?page=wps_locator_update&id=' . $row->id); ?>">View/Edit</a></td>
	                </tr>
	            <?php } ?>
	        </table>
	        <form style="margin-top: 20px;" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <input type='submit' name="export" value='Export' class='button'>
            </form>
	    </div>
    </div>
    <?php
}

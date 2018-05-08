<?php

function wps_subscriber_list() {
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/wps-subscriber/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Subscribers</h2>
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . "wps_subscribers";

        $rows = $wpdb->get_results("SELECT id, SubscriberEmail, SubscriberCreated from $table_name");
        ?>
        <div class="responsive-table">
	        <table class='widefat fixed striped posts'>
	            <tr>
	                <th class="manage-column wps-list-width">ID</th>
	                <th class="manage-column wps-list-width">Email</th>
	                <th class="manage-column wps-list-width">Created</th>
	            </tr>
	            <?php foreach ($rows as $row) { ?>
	                <tr class="">
	                    <td class="manage-column wps-list-width"><?php echo $row->id; ?></td>
	                    <td class="manage-column wps-list-width"><?php echo $row->SubscriberEmail; ?></td>
	                    <td class="manage-column wps-list-width"><?php echo $row->SubscriberCreated; ?></td>
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

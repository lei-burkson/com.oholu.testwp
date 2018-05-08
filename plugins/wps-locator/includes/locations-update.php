<?php

function wps_locator_update() {
    global $wpdb;
    $table_name = $wpdb->prefix . "wps_locations";
    $id = $_GET["id"];

    if (isset($_POST['update'])) {
	    $name = isset($_POST["name"]) ? trim($_POST["name"]) : '';
	    $number = isset($_POST["number"]) ? trim($_POST["number"]) : '';
	    $address = isset($_POST["address"]) ? trim($_POST["address"]) : '';
	    $address2 = isset($_POST["address2"]) ? trim($_POST["address2"]) : '';
	    $city = isset($_POST["city"]) ? trim($_POST["city"]) : '';
	    $state = isset($_POST["state"]) ? trim($_POST["state"]) : '';
	    $zip = isset($_POST["zip"]) ? trim($_POST["zip"]) : '';
	    $phone = isset($_POST["phone"]) ? trim($_POST["phone"]) : '';
	    $country = isset($_POST["country"]) ? trim($_POST["country"]) : '';
	    $lat = isset($_POST["lat"]) ? trim($_POST["lat"]) : '';
	    $lng = isset($_POST["lng"]) ? trim($_POST["lng"]) : '';

	    if (empty($name) || empty($number) || empty($address) || empty($city) || empty($lat) || empty($lng)) {
    		$message .= 'Location not updated due to missing required field(s).';
    		$messageClass = 'error';
    	}
    	else {
			//update
	        $updateResult = $wpdb->update(
	                $table_name, //table
	                array('LocationNumber' => $number, 'LocationName' => $name, 'LocationAddress' => $address, 'LocationAddress2' => $address2,
	                'LocationCity' => $city, 'LocationState' => $state, 'LocationZip' => $zip, 'LocationPhone' => $phone,
	                'LocationCountry' => $country, 'LocationLatitude' => $lat, 'LocationLongitude' => $lng), //data
	                array('id' => $id), //where
	                array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f'), //data format
	                array('%d') //where format
	        );

	        if ($updateResult === false) {
	        	$message.="No location was updated due to invalid field(s).";
	        	$messageClass = 'error';
	        }
	        else {
	       		$message .= 'Location updated.';
    			$messageClass = 'updated';
	       	}
	    }
    }
//delete
    else if (isset($_POST['delete'])) {
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %d", $id));
        $message .= 'Location deleted.';
    	$messageClass = 'updated';
    } else {//selecting value to update
        $locations = $wpdb->get_results($wpdb->prepare("SELECT id, LocationNumber, LocationName, LocationAddress, LocationAddress2, LocationCity,
            LocationState, LocationZip, LocationPhone, LocationCountry, LocationLatitude, LocationLongitude FROM $table_name WHERE id=%d", $id));
        foreach ($locations as $s) {
            $number = $s->LocationNumber;
            $name = $s->LocationName;
            $address = $s->LocationAddress;
            $address2 = $s->LocationAddress2;
            $city = $s->LocationCity;
            $state = $s->LocationState;
            $zip = $s->LocationZip;
            $phone = $s->LocationPhone;
            $country = $s->LocationCountry;
            $lat = $s->LocationLatitude;
            $lng = $s->LocationLongitude;
        }
    }
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/wps-locator/style-admin.css" rel="stylesheet" />
    <div class="wrap wps-update-page">
        <h2>Locations</h2>

        <?php if (isset($message)) : ?>
            <div class="<?php echo $messageClass; ?>"><p><?php echo $message; ?></p></div>
            <?php if ($messageClass == 'updated') : ?>
            <a href="<?php echo admin_url('admin.php?page=wps_locator_list') ?>">&laquo; Back to locations list</a>
        	<?php endif;
        endif;
        	?>

        <?php if (!isset($message) || $messageClass == 'error') :?>
        	<p>* Required Field. Location Number is a unique identifier.</p>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <table class='wp-list-table widefat fixed'>
                    <tr>
                    	<th class="wps-th-width">Location Number *</th>
                    	<td><input type="text" name="number" value="<?php echo $number; ?>" class="wps-field-width"/></td>
                    </tr>
                    <tr>
                    	<th class="wps-th-width">Location Name *</th>
                    	<td><input type="text" name="name" value="<?php echo $name; ?>" class="wps-field-width"/></td>
                    </tr>
                    <tr>
                    	<th class="wps-th-width">Location Address *</th>
                    	<td><input type="text" name="address" value="<?php echo $address; ?>" class="wps-field-width"/></td>
                    </tr>
                    <tr>
                    	<th class="wps-th-width">Location Address2</th>
                    	<td><input type="text" name="address2" value="<?php echo $address2; ?>" class="wps-field-width"/></td>
                    </tr>
                    <tr>
                    	<th class="wps-th-width">Location City *</th>
                    	<td><input type="text" name="city" value="<?php echo $city; ?>" class="wps-field-width"/></td>
                    </tr>
                    <tr>
                    	<th class="wps-th-width">Location State</th>
                    	<td><input type="text" name="state" value="<?php echo $state; ?>" class="wps-field-width"/></td>
                    </tr>
                    <tr>
                    	<th class="wps-th-width">Location Zip</th>
                    	<td><input type="text" name="zip" value="<?php echo $zip; ?>" class="wps-field-width"/></td>
                    </tr>
                    <tr>
                    	<th class="wps-th-width">Location Phone</th>
                    	<td><input type="text" name="phone" value="<?php echo $phone; ?>" class="wps-field-width"/></td>
                    </tr>
                    <tr>
                    	<th class="wps-th-width">Location Country</th>
                    	<td><input type="text" name="country" value="<?php echo $country; ?>" class="wps-field-width"/></td>
                    </tr>
                    <tr>
                    	<th class="wps-th-width">Location Lattitude *</th>
                    	<td><input type="text" name="lat" value="<?php echo $lat; ?>" class="wps-field-width"/></td>
                    </tr>
                    <tr>
                    	<th class="wps-th-width">Location Longitude *</th>
                    	<td><input type="text" name="lng" value="<?php echo $lng; ?>" class="wps-field-width"/></td>
                    </tr>
                </table>
                <input type='submit' name="update" value='Save' class='button'> &nbsp;&nbsp;
                <input type='submit' name="delete" value='Delete' class='button' onclick="return confirm('Do you really want to delete this location?')">
            </form>
        <?php endif; ?>

    </div>
    <?php
}

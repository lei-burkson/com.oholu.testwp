<?php
function wps_locator_import() {
    if (isset($_POST['import']) && isset($_FILES['userfile'])) {
		function processCSV ($fileName) {
			$dataArray = array();
			$row = 1;
			$headers = array();

			if (($handle = fopen($fileName, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
					if ($row === 1) {
						$headers = $data;
					}
					else {
						$rowArray = array();
						for ($i=0; $i < count($headers); $i++) $rowArray[$headers[$i]] = $data[$i];
						$dataArray[] = $rowArray;
					}
					$row++;
				}
				fclose($handle);
			} else return FALSE;

			return $dataArray;
		}

		$userfile = processCSV($_FILES['userfile']['tmp_name']);

		$failedLineArray = array();

		$lineNumber = 1;

		foreach ($userfile as $locationEntry) {
			$name = trim($locationEntry["Name"]);
		    $number = trim($locationEntry["Number"]);
		    $address = trim($locationEntry["Address"]);
		    $address2 = trim($locationEntry["Address2"]);
		    $city = trim($locationEntry["City"]);
		    $state = trim($locationEntry["State"]);
		    $zip = trim($locationEntry["Zip"]);
		    $phone = trim($locationEntry["Phone"]);
		    $country = trim($locationEntry["Country"]);
		    $lat = trim($locationEntry["Latitude"]);
		    $lng = trim($locationEntry["Longitude"]);

		    if (empty($name) || empty($number) || empty($address) || empty($city) || empty($lat) || empty($lng)) {
	    		array_push($failedLineArray, $lineNumber);
	    	}
	    	else {
	    		global $wpdb;
	        	$table_name = $wpdb->prefix . "wps_locations";

		        // mimic insert on duplicate key update
		        $locations = $wpdb->get_results($wpdb->prepare("SELECT LocationNumber FROM $table_name WHERE LocationNumber=%s", $number));

		        if (count($locations) > 0) {
		        	$updateResult = $wpdb->update(
		                $table_name, //table
		                array('LocationName' => $name, 'LocationAddress' => $address, 'LocationAddress2' => $address2,
		                'LocationCity' => $city, 'LocationState' => $state, 'LocationZip' => $zip, 'LocationPhone' => $phone,
		                'LocationCountry' => $country, 'LocationLatitude' => $lat, 'LocationLongitude' => $lng), //data
		                array('LocationNumber' => $number), //where
		                array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f'), //data format
		                array('%s') //where format
			        );
		        }
		        else {
		        	$insertResult = $wpdb->insert(
		                $table_name, //table
		                array('LocationNumber' => $number, 'LocationName' => $name, 'LocationAddress' => $address,
		                'LocationAddress2' => $address2, 'LocationCity' => $city, 'LocationState' => $state,
		                'LocationZip' => $zip, 'LocationPhone' => $phone, 'LocationCountry' => $country,
		                'LocationLatitude' => $lat, 'LocationLongitude' => $lng), //data
		                array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f') //data format
			        );
		        }
	    	}

	    	$lineNumber++;
		}
?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/wps-locator/style-admin.css" rel="stylesheet" />
    <div class="wrap wps-create-page">
        <h2>Import Locations</h2>

		<div class="updated"><p>CSV has been imported.</p></div>

		<?php if (count($failedLineArray)) : ?>
		<div class="error"><p>Failed Line Numbers: <?php echo implode(', ', $failedLineArray);?></p></div>
		<?php endif; ?>
        <a href="<?php echo admin_url('admin.php?page=wps_locator_list') ?>">&laquo; Back to locations list</a>
    </div>
<?php
    }
    else {
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/wps-locator/style-admin.css" rel="stylesheet" />
    <div class="wrap wps-create-page">
        <h2>Import Locations</h2>
        <p>Import Locations from CSV file.</p>
        <form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        	<label>Import CSV: <input name="userfile" type="file" /></label>
            <input type='submit' name="import" value='Import' class='button'>
        </form>
    </div>
    <?php
	}
}

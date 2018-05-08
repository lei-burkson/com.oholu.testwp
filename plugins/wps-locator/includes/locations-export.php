<?php
function wps_locator_export() {
	global $wpdb;
    $table_name = $wpdb->prefix . "wps_locations";

    $rows = $wpdb->get_results("SELECT id, LocationNumber, LocationName, LocationAddress, LocationAddress2, LocationCity,
        LocationState, LocationZip, LocationPhone, LocationCountry, LocationLatitude, LocationLongitude from $table_name");

    if (isset($_POST['export'])) {
		$csvMap = array(
			"ID" => "id",
			"Number" => "LocationNumber",
			"Name" => "LocationName",
			"Address" => "LocationAddress",
			"Address2" => "LocationAddress2",
			"City" => "LocationCity",
			"State" => "LocationState",
			"Zip" => "LocationZip",
			"Phone" => "LocationPhone",
			"Country" => "LocationCountry",
			"Latitude" => "LocationLatitude",
			"Longitude" => "LocationLongitude"
		);

		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=Locations-".time().".csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		function outputCSV($array) {
		    $fp = fopen('php://output', 'w');
		    fputcsv($fp, $array);
		    fclose($fp);
		}
		function getCSV($array) {
		    ob_start();
		    outputCSV($array);
		    return ob_get_clean();
		}

		echo getCSV(array_keys($csvMap));

		foreach ($rows as $row) {
			$outputArray = array();
			foreach ($csvMap as $columnName => $fieldName) {
				array_push($outputArray, $row->$fieldName);
			}

			echo getCSV($outputArray);
		}
		die;
	}
}

add_action( 'admin_init', 'wps_locator_export' );
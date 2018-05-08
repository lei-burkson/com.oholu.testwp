<?php
function wps_locator_export() {
	global $wpdb;
    $table_name = $wpdb->prefix . "wps_subscribers";

    $rows = $wpdb->get_results("SELECT id, SubscriberEmail, SubscriberName, SubscriberAddress, SubscriberAddress2, SubscriberCity,
        SubscriberState, SubscriberZip, SubscriberPhone, SubscriberCountry, SubscriberUnsubscribed, SubscriberToken, SubscriberCreated, SubscriberLastUpdated from $table_name");

    if (isset($_POST['export'])) {
		$csvMap = array(
			"ID" => "id",
			"Email" => "SubscriberEmail",
			"Name" => "SubscriberName",
			"Address" => "SubscriberAddress",
			"Address2" => "SubscriberAddress2",
			"City" => "SubscriberCity",
			"State" => "SubscriberState",
			"Zip" => "SubscriberZip",
			"Phone" => "SubscriberPhone",
			"Country" => "SubscriberCountry",
			"Unsubscribed" => "SubscriberUnsubscribed",
			"Token" => "SubscriberToken",
			"SubscriberCreated" => "SubscriberToken",
			"SubscriberLastUpdated" => "SubscriberToken"
		);

		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=Subscribers-".time().".csv");
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

add_action( 'admin_init', 'wps_subscriber_export' );
<?php
/**
 * Add Location search
 */

/**
 * Calculates the great-circle distance between two points, with
 * the Vincenty formula.
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param float $earthRadius Mean earth radius in [mile]
 * @return float Distance between points in [mile] (same as earthRadius)
 */
function wpsVincentyGreatCircleDistance ( $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 3959)
{
	// convert from degrees to radians
	$latFrom = deg2rad($latitudeFrom);
	$lonFrom = deg2rad($longitudeFrom);
	$latTo = deg2rad($latitudeTo);
	$lonTo = deg2rad($longitudeTo);

	$lonDelta = $lonTo - $lonFrom;
	$a = pow(cos($latTo) * sin($lonDelta), 2) +
	pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
	$b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

	$angle = atan2(sqrt($a), $b);
	return $angle * $earthRadius;
}

/**
 * Validates a given latitude $lat
 *
 * @param float|int|string $lat Latitude
 * @return bool `true` if $lat is valid, `false` if not
 */
function wpsValidateLatitude($lat) {
	return preg_match('/^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,32})?))$/', $lat);
}

/**
 * Validates a given longitude $long
 *
 * @param float|int|string $long Longitude
 * @return bool `true` if $long is valid, `false` if not
 */
function wpsValidateLongitude($long) {
	return preg_match('/^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,32})?))$/', $long);
}

// The function that handles the AJAX request
function wps_get_locations() {
	check_ajax_referer( 'wps-locator-nonce', 'security' );

	//$theID = $_REQUEST['id'];
	// Get requested starting lat and lng
	$lat = $_GET['lat'];
	$lng = $_GET['lng'];
	$radius = empty($_GET['radius']) ? 25 : floatval($_GET['radius']);
	$cnt = empty($_GET['cnt']) ? 10 : intval($_GET['cnt'], 10);

	if (empty($lat) || empty($lng) || !wpsValidateLongitude($lng) || !wpsValidateLatitude($lat) || !$radius || !$cnt) {
		echo json_encode(array('error' => 'Missing or invalid parameters.'));
		die();
	}

	global $wpdb;
	$table_name = $wpdb->prefix . "wps_locations";

	$rows = $wpdb->get_results("SELECT id, LocationNumber, LocationName, LocationAddress, LocationAddress2, LocationCity,
	LocationState, LocationZip, LocationPhone, LocationCountry, LocationLatitude, LocationLongitude from $table_name");

	$outPosts = array();

	foreach ($rows as $key => $post) {
		$destLat = $post->LocationLatitude;
		$destLng = $post->LocationLongitude;

		$distance = wpsVincentyGreatCircleDistance($lat, $lng, $destLat, $destLng);
		$post->Distance = $distance;
		if ($distance <= $radius && count($outPosts) < $cnt) {
			array_push($outPosts, $post);
		}
	}

	if ( empty( $outPosts ) ) {
		echo json_encode(array('error' => 'No results returned.'));
		die();
	}

	echo json_encode($outPosts);

	die(); // this is required to return a proper result
}
add_action( 'wp_ajax_wps_get_locations', 'wps_get_locations' );
add_action( 'wp_ajax_nopriv_wps_get_locations', 'wps_get_locations' );

// The function that handles the AJAX request
function wps_locations_in_bound() {
	check_ajax_referer( 'wps-locator-nonce', 'security' );

	// Get requested starting bound min_lat, max_lat, min_lng, max_lng
	$minLat = $_GET['min_lat'];
	$maxLat = $_GET['max_lat'];
	$minLng = $_GET['min_lng'];
	$maxLng = $_GET['max_lng'];

	if (empty($minLat) || empty($maxLat) || empty($minLng) || empty($maxLng) || !wpsValidateLatitude($minLat) || !wpsValidateLongitude($minLng) || !wpsValidateLatitude($maxLat) || !wpsValidateLongitude($maxLng)) {
		echo json_encode(array('error' => 'Missing or invalid parameters.'));
		die();
	}

	global $wpdb;
	$table_name = $wpdb->prefix . "wps_locations";

	$rows = $wpdb->get_results($wpdb->prepare("SELECT id, LocationNumber, LocationName, LocationAddress, LocationAddress2, LocationCity,
            LocationState, LocationZip, LocationPhone, LocationCountry, LocationLatitude, LocationLongitude FROM $table_name WHERE LocationLatitude >= %f AND
            LocationLatitude <= %f AND LocationLongitude >= %f AND LocationLongitude <= %f", array($minLat, $maxLat, $minLng, $maxLng)));

	if ( empty( $rows ) ) {
		echo json_encode(array('error' => 'No results returned.'));
		die();
	}

	echo json_encode($rows);

	die(); // this is required to return a proper result
}
add_action( 'wp_ajax_wps_locations_in_bound', 'wps_locations_in_bound' );
add_action( 'wp_ajax_nopriv_wps_locations_in_bound', 'wps_locations_in_bound' );

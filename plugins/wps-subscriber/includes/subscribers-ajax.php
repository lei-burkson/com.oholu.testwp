<?php
function crypto_rand_secure($min, $max) {
    $range = $max - $min;
    if ($range < 1) return $min;
    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1;
    $bits = (int) $log + 1;
    $filter = (int) (1 << $bits) - 1;
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter;
    } while ($rnd > $range);
    return $min + $rnd;
}

function getToken($length)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, $max-1)];
    }

    return $token;
}

// The function that handles the AJAX request
function wps_post_subscriber() {
	check_ajax_referer( 'wps-subscriber-nonce', 'security' );

	// Parameters
    $name = isset($_POST["name"]) ? trim($_POST["name"]) : '';
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
    $address = isset($_POST["address"]) ? trim($_POST["address"]) : '';
    $address2 = isset($_POST["address2"]) ? trim($_POST["address2"]) : '';
    $city = isset($_POST["city"]) ? trim($_POST["city"]) : '';
    $state = isset($_POST["state"]) ? trim($_POST["state"]) : '';
    $zip = isset($_POST["zip"]) ? trim($_POST["zip"]) : '';
    $phone = isset($_POST["phone"]) ? trim($_POST["phone"]) : '';
	$country = isset($_POST["country"]) ? trim($_POST["country"]) : '';
	
	//Custom variables
	$token = getToken(32);

	// Required fields check
	if (empty($email)) {
		echo json_encode(array('error' => 'Email is required.'));
		die();
	}

	global $wpdb;
	$table_name = $wpdb->prefix . "wps_subscribers";

	$sql = "INSERT INTO $table_name (SubscriberEmail, SubscriberName, SubscriberAddress, SubscriberAddress2, SubscriberCity, SubscriberState, SubscriberZip, SubscriberPhone, SubscriberCountry, SubscriberUnsubscribed, SubscriberToken, SubscriberCreated) 
	VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, now()) ON DUPLICATE KEY UPDATE SubscriberEmail = %s";
	$sql = $wpdb->prepare($sql, array($email, $name, $address, $address2, $city, $state, $zip, $phone, $country, 'NO', $token, $email));
	$queryResult = $wpdb->query($sql);

	if ($insertResult === false || $insertResult === 0) {
		echo json_encode(array('error' => 'Subscription failed.'));
		die();
	}
	else {
		echo json_encode(array('error' => 'Subscription failed.'));
		die();
	}

	die(); // this is required to return a proper result
}
add_action( 'wp_ajax_wps_post_subscriber', 'wps_post_subscriber' );
add_action( 'wp_ajax_nopriv_wps_post_subscriber', 'wps_post_subscriber' );
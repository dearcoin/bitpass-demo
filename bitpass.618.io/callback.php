<?php
/**
 * callback
 *
 * @author panzhibiao@bitfund.pe
 * @since 2013-08
 * @lastmodify 2013-11
 */
define('IN_SYSTEM', 1);
require_once dirname(__FILE__).'/common.php';

// args
$signature = isset($_REQUEST['signature']) ? $_REQUEST['signature'] : null;
$address   = isset($_REQUEST['address'])   ? $_REQUEST['address']   : null;
$message   = isset($_REQUEST['message'])   ? $_REQUEST['message']   : null;

// extra info
$latitude  = isset($_REQUEST['latitude'])  ? $_REQUEST['latitude']  : null;
$longitude = isset($_REQUEST['longitude']) ? $_REQUEST['longitude'] : null;

if (empty($signature) || empty($address) || empty($message)) {
	die("-1\tinvalid arguments");
}


//
// you can do something to check the message if you put data in it.
//
// $message_arr = explode(':', $message);
// if (empty($message_arr[0]) || time() - $message_arr[0] > 30*60) {
// 	// expire time is 30 minutes
// 	die("-1\tmessage expired");
// }


// find the item by source_message
$sql = "SELECT * FROM `bitpass_messages` 
WHERE source_message = :source_message AND (btc_address = '' OR btc_address = NULL) ";
$item = DB::pfetch($sql, array(':source_message' => $message));
if (empty($item)) {
	die("-1\tcan't find bitpass_messages item OR already used");
}

try {
	if (!isMessageSignatureValid($address, $signature, $message)) {
		die("-1\tsignature invalid");
	}
} catch (Exception $e) {
    die("-1\t".$e->getMessage());
}

// update the item
$sql = " UPDATE `bitpass_messages` 
SET btc_address      = :btc_address,
	signature_base64 = :signature_base64,
	verify_time      = :verify_time,
	latitude         = :latitude,
	longitude        = :longitude
WHERE bitpass_message_id = :bitpass_message_id ";
$arr = array(
		':btc_address'        => $address,
		':signature_base64'   => $signature,
		':bitpass_message_id' => $item['bitpass_message_id'],
		':verify_time'        => date('Y-m-d H:i:s'),
		':latitude'           => $latitude  > 0 ? $latitude  : 0,
		':longitude'          => $longitude > 0 ? $longitude : 0,
		);
if (DB::pexec($sql, $arr, DB::RETURN_AFFECTED_ROWS) == 0) {
	die("-1\tupdate bitpass_messages failure");
}

// success
echo "1\tOK";

<?php
/**
 * try login
 *
 * @author panzhibiao@bitfund.pe
 * @since 2013-08
 * @lastmodify 2013-11
 */
define('IN_SYSTEM', 1);
require_once dirname(__FILE__).'/common.php';

$message = isset($_REQUEST['message']) ? $_REQUEST['message'] : null;
if (empty($message)) {
	die("-1\tinvalid arguments");
}

// find the item by source_message
$sql = "SELECT * FROM `bitpass_messages` WHERE source_message = :source_message ";
$item = DB::pfetch($sql, array(':source_message' => $message));
if (empty($item)) {
	die("-1\tcan't find bitpass_messages item");
}
if (empty($item['btc_address'])) {
	die("-1\tnot verify yet");
}

//
// do login staff, write cookie etc.
// 
setcookie('bitpass_message', rawurlencode($message), 0, '/');

// success
echo "1\t{$item['btc_address']}";

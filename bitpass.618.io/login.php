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

if (empty($_COOKIE['bitpass_message'])) {
	die("you are not login yet");
}
$message = rawurldecode($_COOKIE['bitpass_message']);

// find the item by source_message
$sql = "SELECT * FROM `bitpass_messages` WHERE source_message = :source_message ";
$item = DB::pfetch($sql, array(':source_message' => $message));
if (empty($item) || empty($item['btc_address'])) {
	die("you are not login yet");
}

echo "hello, ", $item['btc_address'], "\n";
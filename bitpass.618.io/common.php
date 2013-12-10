<?php
/**
 * commmon
 * 
 * @author panzhibiao@bitfund.pe
 * @since 2013-08
 * @lastmodify 2013-11
 */
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

ob_start();
ini_set('include_path', dirname(__FILE__));
date_default_timezone_set('UTC');

require_once 'config.php';
require_once 'include/verifymessage.php';

get_db();










/**************************** functions & classes *****************************/


function bitpass_insert_new_message() {
	$sql = " INSERT INTO `bitpass_messages`(
		`source_message`, `btc_address`, `signature_base64`, `creation_time`)
	VALUES (:source_message, :btc_address, :signature_base64, :creation_time)
	";
	$msg = time().':'.random_string(32);
	$arr = array(
			':source_message'   => $msg,
			':btc_address'      => '',
			':signature_base64' => '',
			':creation_time'    => date('Y-m-d H:i:s'),
	);
	if (DB::pexec($sql, $arr, DB::RETURN_AFFECTED_ROWS) == 0) {
		die("[bitpass_insert_new_message] insert new message error");
	}
	return $msg;
}


function random_string($len) {
	static $s = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$sl = strlen($s) - 1;
	$r = '';
	for ($i = 0; $i < $len; $i++) {
		$r .= $s[rand(0, $sl)];
	}
	return $r;
}


/**
 * get db handler, default
 */
function get_db() {
	global $_G, $_CFG;
	if (!empty($_G['db'])) {
		return $_G['db'];
	}

	$opt = &$_CFG['db'];  // alias config['db']
	try {
		$_G['db'] = new PDO($opt['dsn'], $opt['username'], $opt['password'],
				$opt['options']);
		unset($_CFG['db']);
	} catch (PDOException $e) {
		die('Connection DB failed: ' . $e->getMessage());
	}
	return $_G['db'];
}



/**
 * DB common handle
 */
class DB {
	const RETURN_AFFECTED_ROWS = 1;

	static function pexec($sql, $arr, $return_affected_count=0) {
		$db = get_db();
		$sth = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		if ($sth->execute($arr) == false) {
			$errInfo = $sth->errorInfo();
			die("pexec() error: {$errInfo[0]}, {$errInfo[1]}, {$errInfo[2]}");
		}
		if ($return_affected_count) {
			return $sth->rowCount();
		}
		return true;
	}

	static function pfetch($sql, $arr) {
		$db = get_db();
		$sth = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		if ($sth->execute($arr) == false) {
			$errInfo = $sth->errorInfo();
			die("pfetch() error: {$errInfo[0]}, {$errInfo[1]}, {$errInfo[2]}");
		}
		return $sth->fetch(PDO::FETCH_ASSOC);
	}

	static function pfetch_all($sql, $arr) {
		$db = get_db();
		$sth = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		if ($sth->execute($arr) == false) {
			$errInfo = $sth->errorInfo();
			die("pfetch_all() error: {$errInfo[0]}, {$errInfo[1]}, {$errInfo[2]}");
		}
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}
}


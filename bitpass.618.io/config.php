<?php
/**
 * config
 * 
 * @author panzhibiao@bitfund.pe
 * @since 2013-08
 * @lastmodify 2013-11
 */
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

$_CFG = array();

// mysql db
$_CFG['db'] = array(
		'dsn' => 'mysql:host=localhost;port=3306;dbname=BitPassDB',
		'username' => 'bitpass',
		'password' => 'ZwVjYzLfZTREFh4d',
		'options'  => array(
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		)
);

//$_CFG['websocket_host'] = "http://node4bitpass.618.io/";
$_CFG['callback_url']   = "http://bitpass.618.io/callback.php";

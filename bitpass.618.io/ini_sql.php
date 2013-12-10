<?php
/**
 * @author panzhibiao@bitfund.pe
 * @since 2013-08
 * @lastmodify 2013-11
 */
exit;
?>

-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `BitPassDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

CREATE USER 'bitpass'@'localhost' IDENTIFIED BY  'ZwVjYzLfZTREFh4d';
GRANT USAGE ON * . * TO  'bitpass'@'localhost' IDENTIFIED BY  'ZwVjYzLfZTREFh4d' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;
GRANT SELECT , INSERT , UPDATE , DELETE ON  `BitPassDB` . * TO  'bitpass'@'localhost';

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `bitpass_messages` (
  `bitpass_message_id` int(11) NOT NULL AUTO_INCREMENT,
  `source_message` varchar(64) COLLATE utf8_bin NOT NULL,
  `btc_address` varchar(35) COLLATE utf8_bin NOT NULL,
  `signature_base64` char(88) COLLATE utf8_bin NOT NULL,
  `verify_time` datetime DEFAULT NULL,
  `latitude` decimal(15,12) DEFAULT NULL,
  `longitude` decimal(15,12) DEFAULT NULL,
  `creation_time` datetime NOT NULL,
  PRIMARY KEY (`bitpass_message_id`),
  UNIQUE KEY `source_message` (`source_message`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
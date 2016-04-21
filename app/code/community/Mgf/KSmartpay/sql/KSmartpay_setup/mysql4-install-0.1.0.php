<?php

$installer = $this;

$installer->startSetup();

$installer->run("
	CREATE TABLE `ksmartpay_transaction` (
	`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`order_increment_id` varchar(100) not null,
	`reference_number` varchar(100) not null,
	
	PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
");

$installer->endSetup();
?>
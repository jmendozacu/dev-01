<?php

$installer=$this;
$installer->startSetup();
 
$installer->run("

CREATE TABLE `tbl_sync_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `log` text COLLATE utf8mb4_unicode_ci NOT NULL,
  message text COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


");
 
$installer->endSetup();
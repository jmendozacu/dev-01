<?php 

$installer = $this;
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS `tbl_log_pim`;
CREATE TABLE `tbl_log_pim` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `end_time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `results` text,
  `status` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

");

$installer->endSetup();
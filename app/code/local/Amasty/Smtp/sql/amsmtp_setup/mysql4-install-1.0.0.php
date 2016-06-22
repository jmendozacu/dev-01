<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
$installer = $this;
$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('amsmtp/log')}` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cdate` datetime NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `recipient_name` varchar(150) NOT NULL,
  `recipient_email` varchar(120) NOT NULL,
  `template_code` varchar(150) NOT NULL,
  `status` enum('sent','failed') NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
");

$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('amsmtp/debug')}` (
  `debug_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cdate` datetime NOT NULL,
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`debug_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
");
$installer->endSetup();
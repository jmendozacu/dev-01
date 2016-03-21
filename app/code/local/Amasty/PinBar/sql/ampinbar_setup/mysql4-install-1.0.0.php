<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_PinBar
 */


$this->startSetup();

$this->run("
 CREATE TABLE if not exists `{$this->getTable('ampinbar/pinbar')}` (
  `pin_id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) unsigned NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`pin_id`),
  KEY `FK_amasty_ampinbar_admin_user` (`user_id`),
  CONSTRAINT `FK_amasty_ampinbar_admin_user` FOREIGN KEY (`user_id`) REFERENCES `{$this->getTable('admin_user')}` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE if not exists `{$this->getTable('ampinbar/session')}` (
  `pin_id` int(10) unsigned NOT NULL,
  `session` text NOT NULL,
  KEY `FK_amasty_ampinbar_session_data_amasty_ampinbar` (`pin_id`),
  CONSTRAINT `FK_amasty_ampinbar_session_data_amasty_ampinbar` FOREIGN KEY (`pin_id`) REFERENCES `{$this->getTable('ampinbar/pinbar')}` (`pin_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
");

$this->endSetup();
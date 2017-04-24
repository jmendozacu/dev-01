<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('franchise')};
CREATE TABLE {$this->getTable('franchise')} (
 `franchise_id` int(11) unsigned NOT NULL auto_increment,
 `contact_name` varchar(150) NOT NULL DEFAULT '',
 `company_name` varchar(150) NULL DEFAULT '',
 `address` varchar(150) NULL DEFAULT '',
 `country` varchar(150) NULL DEFAULT '',
 `tel` varchar(150) NULL DEFAULT '',
 `fax` varchar(150) NULL DEFAULT '',
 `website` varchar(150) NULL DEFAULT '',
 `email` varchar(150) NULL DEFAULT '',
 `current_business` text NULL DEFAULT '',
 `retail_business` text NULL DEFAULT '',
 `number_staff` text NULL DEFAULT '',
 `sale_turnover` text NULL DEFAULT '',
 `warehouse` text NULL DEFAULT '',
 `experience` text NULL DEFAULT '',
 `potential_competitor` text NULL DEFAULT '',
 `potential_locations` text NULL DEFAULT '',
 PRIMARY KEY (`franchise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Franchise Application';
");

$installer->endSetup(); 
<?php

$installer = $this;
$installer->startSetup();

$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('import_customer_temp')};
  
	CREATE TABLE {$this->getTable('import_customer_temp')} (
	  `import_temp_id` int(11) unsigned NOT NULL auto_increment,
	  `customer_id` int(11) NULL,
	  `website_id` smallint(5) NULL,
    `group_id` smallint(5) NULL,
    `firstname` varchar(255) NOT NULL default '',
    `lastname` varchar(255) NOT NULL default '',
    `email` varchar(255) NOT NULL default '',
    `password` varchar(255) NOT NULL default '',
    `country_id` varchar(11) NULL,
    `region_id` varchar(11) NULL,
    `city` varchar(11) NULL,
    `subdistrict_id` varchar(11) NULL,
    `postcode` varchar(11) NULL,
    `street` varchar(255) NULL,
    `telephone` varchar(12) NULL,
	  PRIMARY KEY (`import_temp_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 
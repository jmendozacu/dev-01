<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('career_worktype')};
CREATE TABLE {$this->getTable('career_worktype')} (
 `worktype_id` int(11) unsigned NOT NULL auto_increment,
 `title` varchar(255) NULL,
 `status` smallint(6) NOT NULL default '0',
 PRIMARY KEY (`worktype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Career Work Type';
");

$installer->endSetup(); 
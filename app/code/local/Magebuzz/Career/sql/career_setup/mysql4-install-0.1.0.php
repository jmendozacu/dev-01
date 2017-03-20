<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('career_job')};
CREATE TABLE {$this->getTable('career_job')} (
 `job_id` int(11) unsigned NOT NULL auto_increment,
 `title` text NULL DEFAULT '',
 `function` int(11) NULL,
 `position` text NULL DEFAULT '',
 `location` text NULL DEFAULT '',
 `scope_of_work` text NULL DEFAULT '',
 `qualifications` text NULL DEFAULT '',
 `status` smallint(6) NOT NULL default '0',
 PRIMARY KEY (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Career Job';

DROP TABLE IF EXISTS {$this->getTable('career_application')};
CREATE TABLE {$this->getTable('career_application')} (
 `application_id` int(11) unsigned NOT NULL auto_increment,
 `identity_number` int(11) NULL,
 `application_for_job_id` int(11) unsigned NOT NULL,
 `email` varchar(150) NULL DEFAULT '',
 `name` varchar(150) NOT NULL DEFAULT '',
 `surname` varchar(150) NOT NULL DEFAULT '',
 `tel` varchar(150) NULL DEFAULT '',
 `date_of_birth` datetime NULL,
 `work_type` varchar(150) NOT NULL DEFAULT '',
 `work_age` int(11)  NULL,
 `present_company` varchar(150) NOT NULL DEFAULT '',
 `last_position` varchar(150) NOT NULL DEFAULT '',
 PRIMARY KEY (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 
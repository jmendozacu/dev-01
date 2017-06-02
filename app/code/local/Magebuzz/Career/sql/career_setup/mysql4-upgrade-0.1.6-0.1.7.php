<?php

$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('career_worktype_store')} (
   `worktype_id` int(10) UNSIGNED NOT NULL,
   `store_id` smallint(5) UNSIGNED NOT NULL,
   KEY `FK_CAREER_WORKTYPE_STORE` (`worktype_id`),
   CONSTRAINT `FK_CAREER_WORKTYPE_STORE` FOREIGN KEY (`worktype_id`) REFERENCES `{$this->getTable('career/worktype')}` (`worktype_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
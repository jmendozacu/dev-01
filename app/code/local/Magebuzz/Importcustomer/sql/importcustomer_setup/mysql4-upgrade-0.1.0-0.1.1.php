<?php
$installer=$this;
$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('import_customer_temp')}` ADD `fax` varchar(12) DEFAULT NULL;
ALTER TABLE `{$this->getTable('import_customer_temp')}` ADD `joycard` varchar(20) DEFAULT NULL;
ALTER TABLE `{$this->getTable('import_customer_temp')}` ADD `mobile` varchar(12) DEFAULT NULL;
");

$installer->endSetup();
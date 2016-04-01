<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('dealerlocator')} ADD `store_code` varchar(255) null;
");
$installer->endSetup();
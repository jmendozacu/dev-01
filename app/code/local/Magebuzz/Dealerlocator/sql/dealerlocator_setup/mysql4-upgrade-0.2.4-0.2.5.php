<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('dealerlocator')} ADD `dealer_map` varchar(255) null;
");
$installer->endSetup();
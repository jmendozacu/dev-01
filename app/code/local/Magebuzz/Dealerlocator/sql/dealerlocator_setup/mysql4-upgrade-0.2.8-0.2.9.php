<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('dealerlocator')} ADD `open_time` varchar(255) null;
");
$installer->endSetup();
<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('product_dealer')} ADD `display` int(11) NOT NULL default 0;
");
$installer->endSetup();
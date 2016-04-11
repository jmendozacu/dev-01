<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	DELETE FROM {$this->getTable('product_dealer')};
	ALTER TABLE {$this->getTable('product_dealer')} ADD UNIQUE (product_id,dealer_id);
");
$installer->endSetup();
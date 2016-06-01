<?php
$installer = $this;
$installer->startSetup();
$installer->run("
	ALTER TABLE {$this->getTable('product_dealer_temp')} ADD `store_id` int(11)  NOT NULL default 0 AFTER `store_code`;
	ALTER TABLE {$this->getTable('product_dealer')} ADD `store_id` int(11)  NOT NULL default 0 AFTER `product_id`;
");
$sql = "ALTER TABLE `product_dealer`
DROP INDEX `product_id` ,
ADD UNIQUE INDEX `product_id` (`product_id`, `dealer_id`, `store_id`) USING BTREE ;
";
$installer->run($sql);
$installer->endSetup();
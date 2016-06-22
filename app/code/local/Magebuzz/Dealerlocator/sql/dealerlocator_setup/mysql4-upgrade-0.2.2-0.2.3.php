<?php
$installer = $this;
$installer->startSetup();
$installer->run("
  -- DROP TABLE IF EXISTS {$this->getTable('product_dealer_temp')};
  CREATE TABLE {$this->getTable('product_dealer_temp')} (
    `dealer_id` int(11) unsigned NOT NULL auto_increment,
    `store_code` varchar(255) NOT NULL default 0,
    `product_sku` varchar(255) NOT NULL default 0,
    `product_id` int(11) NOT NULL default 0,
    `display` int(11) NOT NULL default 0,
    PRIMARY KEY (`dealer_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
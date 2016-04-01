<?php
$installer = $this;
$installer->startSetup();
$installer->run("
  -- DROP TABLE IF EXISTS {$this->getTable('product_dealer')};
  CREATE TABLE {$this->getTable('product_dealer')} (
    `productdealer_id` int(11) unsigned NOT NULL auto_increment,
    `product_id` int(11) NOT NULL default 0,
    `dealer_id` int(11) NOT NULL default 0,
    PRIMARY KEY (`productdealer_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
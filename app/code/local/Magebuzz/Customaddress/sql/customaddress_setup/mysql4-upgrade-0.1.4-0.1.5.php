<?php
$installer = $this;
$installer->startSetup();

//$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$tablequote = $this->getTable('sales/quote_address');
$installer->run("
ALTER TABLE  $tablequote ADD  `mobile_customer` varchar(255) NOT NULL
");

$tablequote = $this->getTable('sales/order_address');
$installer->run("
ALTER TABLE  $tablequote ADD  `mobile_customer` varchar(255) NOT NULL
");
$installer->endSetup();

<?php

$installer = $this;

$installer->startSetup();

$installer->addAttribute('order', 'exported_name', array('type' => 'varchar', 'visible' => FALSE));

$installer->endSetup();



/* @var $installer Mage_Core_Model_Resource_Setup */
/*
$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$table = $installer->getTable('sales/order');
$conn->addColumn($table, 'exported_name', 'varchar');

$installer->endSetup();
*/
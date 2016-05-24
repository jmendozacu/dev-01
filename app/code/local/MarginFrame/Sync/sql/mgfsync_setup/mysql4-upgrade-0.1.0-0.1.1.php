<?php
$installer = $this;

$installer->startSetup();

$installer->addAttribute('order_item', 'delivery_method', array('type' => 'varchar', 'visible' => FALSE));
$installer->addAttribute('order_item', 'tracking_no', array('type' => 'varchar', 'visible' => FALSE));
$installer->addAttribute('order_item', 'shipment_no', array('type' => 'varchar', 'visible' => FALSE));

$installer->endSetup();


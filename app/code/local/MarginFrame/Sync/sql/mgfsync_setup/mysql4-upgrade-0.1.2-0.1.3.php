<?php

$installer = $this;
$conn = $installer->getConnection();
 
$conn->addColumn($installer->getTable('sales/quote_address'), 'mobile_no', 'varchar(255) after telephone');
$conn->addColumn($installer->getTable('sales/quote_address'), 'telephone_ex', 'varchar(255) after telephone');

$conn->addColumn($installer->getTable('sales/order_address'), 'mobile_no', 'varchar(255) after telephone');
$conn->addColumn($installer->getTable('sales/order_address'), 'telephone_ex', 'varchar(255) after telephone');

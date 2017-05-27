<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */


/** @var Amasty_Customform_Model_Mysql4_Setup $installer */
$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('amcustomform/form');
$installer->getConnection()->addColumn(
  $tableName,
  'template_email_id',
  'int NULL DEFAULT 0'
);

$installer->endSetup();
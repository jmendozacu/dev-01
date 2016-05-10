<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

$installer = $this;

$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('eav/attribute')}` ADD `customer_group_enabled` TINYINT( 1 ) UNSIGNED NOT NULL ;
");

$installer->endSetup();
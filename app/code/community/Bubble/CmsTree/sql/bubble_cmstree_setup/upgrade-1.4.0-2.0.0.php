<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */

/* @var $installer Bubble_CmsTree_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();
$tablePageTree = $installer->getTable('bubble_cms_page_tree');

$installer->run("
    ALTER TABLE `{$tablePageTree}`
        ADD `manage_versions` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';
");

$installer->endSetup();

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$query = "
    CREATE TABLE IF NOT EXISTS `{$this->getTable('flippingbook/category')}` (
        `category_id`			int(10) unsigned NOT NULL AUTO_INCREMENT,
        `category_name`			varchar(255) NOT NULL,
        `category_description`	text NOT NULL,
        `creation_date`			datetime NOT NULL,
        `update_date`			datetime NOT NULL,
        PRIMARY KEY (`category_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='Flippingbook categories';

     CREATE TABLE IF NOT EXISTS `{$this->getTable('flippingbook/template')}` (
        `template_id`			    int(10) unsigned NOT NULL AUTO_INCREMENT,
        `template_name`			    varchar(255) NOT NULL,
        `root_template`			    varchar(255) NOT NULL,
        `font_family`       	    smallint(2) NOT NULL,
        `font_size`          	    smallint(2) NOT NULL,
        `paragraph_spacing`    	    smallint(2) NOT NULL,
        `line_spacing`      	    smallint(2) NOT NULL,
        `page_background_color`     varchar(10) NOT NULL,
        `background_color`          varchar(10) NOT NULL,
        `text_color`                varchar(10) NOT NULL,
        `display_slider`            smallint(1) NOT NULL,
        `display_pagebox`           smallint(1) NOT NULL,
        `display_title`             smallint(1) NOT NULL,
        `display_top_icons`         smallint(1) NOT NULL,
        `display_move_button`       smallint(1) NOT NULL,
        `creation_date`			    datetime NOT NULL,
        `update_date`			    datetime NOT NULL,
        PRIMARY KEY (`template_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=UTF8 COMMENT='Flippingbook templates';

    CREATE TABLE IF NOT EXISTS `{$this->getTable('flippingbook/resolution')}` (
        `resolution_id`				int(10) unsigned NOT NULL AUTO_INCREMENT,
        `resolution_name`			varchar(250) NOT NULL,
        `resolution_width`			int(10) unsigned NOT NULL DEFAULT '800',
        `resolution_height`			int(10) unsigned NOT NULL DEFAULT '600',
        `creation_date`				datetime NOT NULL,
        `update_date`				datetime NOT NULL,
        PRIMARY KEY (`resolution_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Flippingbook resolutions';

    CREATE TABLE IF NOT EXISTS `{$this->getTable('flippingbook/magazine')}` (
        `magazine_id`					int(10) unsigned NOT NULL AUTO_INCREMENT,
        `magazine_category_id`			int(10) unsigned NOT NULL DEFAULT '0',
        `magazine_template_id`			int(10) unsigned NOT NULL DEFAULT '0',
        `magazine_resolution_id`		int(10) unsigned NOT NULL DEFAULT '0',
        `magazine_title`				varchar(255) NOT NULL DEFAULT '',
        `magazine_enable_pdf`			tinyint(1) NOT NULL DEFAULT '0',
        `magazine_background_pdf`		varchar(250) DEFAULT NULL,
        `magazine_view_mode`			tinyint(1) unsigned NOT NULL DEFAULT '1',
        `magazine_thumbnail`			varchar(250) DEFAULT NULL,
        `magazine_imgsub`				binary(1) DEFAULT '0',
        `magazine_imgsubfolder`			varchar(250) DEFAULT '',
        `magazine_sort_order`			int(11) NOT NULL DEFAULT '0',
        `created_at`  					datetime NOT NULL,
        `updated_at` 					datetime NOT NULL,
        `is_active`						tinyint(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`magazine_id`),
        CONSTRAINT `FK_FLIPPINGBOOK_CATEGORY_ID` FOREIGN KEY (`magazine_category_id`) REFERENCES `{$this->getTable('flippingbook/category')}` (`category_id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `FK_FLIPPINGBOOK_TEMPLATE_ID` FOREIGN KEY (`magazine_template_id`) REFERENCES `{$this->getTable('flippingbook/template')}` (`template_id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `FK_FLIPPINGBOOK_RESOLUTION_ID` FOREIGN KEY (`magazine_resolution_id`) REFERENCES `{$this->getTable('flippingbook/resolution')}` (`resolution_id`) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Flippingbook magazines';

    CREATE TABLE IF NOT EXISTS `{$this->getTable('flippingbook/magazine_store')}` (
        `magazine_id`			int(10) unsigned NOT NULL,
        `store_id`				smallint(5) unsigned NOT NULL,
        PRIMARY KEY (`magazine_id`,`store_id`),
        CONSTRAINT `FK_FLIPPINGBOOK_MAGAZINE_STORE_MAGAZINE_ID` FOREIGN KEY (`magazine_id`) REFERENCES `{$this->getTable('flippingbook/magazine')}` (`magazine_id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `FK_FLIPPINGBOOK_MAGAZINE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=InnoDB COMMENT='Stores and Magazines Relations';

    CREATE TABLE IF NOT EXISTS `{$this->getTable('flippingbook/product_magazine')}` (
        `magazine_id`			int(10) unsigned NOT NULL,
        `entity_id`				int(10) unsigned NOT NULL,
        PRIMARY KEY (`magazine_id`,`entity_id`),
        CONSTRAINT `FK_FLIPPINGBOOK_PRODUCT_MAGAZINE_MAGAZINE_ID` FOREIGN KEY (`magazine_id`) REFERENCES `{$this->getTable('flippingbook/magazine')}` (`magazine_id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `FK_FLIPPINGBOOK_PRODUCT_MAGAZINE_ENTITY_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('catalog/product')}` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=InnoDB COMMENT='Products and Magazines Relations';

    CREATE TABLE IF NOT EXISTS `{$this->getTable('flippingbook/page')}` (
        `page_id`				int(10) unsigned NOT NULL AUTO_INCREMENT,
        `page_magazine_id`		int(10) unsigned unsigned NOT NULL DEFAULT '0',
        `page_title`			varchar(255) DEFAULT NULL,
        `page_type`				varchar(10) DEFAULT NULL,
        `page_sort_order`		int(11) DEFAULT '0',
        `page_image`			varchar(250) DEFAULT NULL,
        `page_text`				text,
        `created_at`			datetime NOT NULL,
        `updated_at`			datetime NOT NULL,
        `is_active`				tinyint(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`page_id`),
        CONSTRAINT `FK_FLIPPINGBOOK_PAGE_MAGAZINE_ID` FOREIGN KEY (`page_magazine_id`) REFERENCES `{$this->getTable('flippingbook/magazine')}` (`magazine_id`) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Magazines Pages';

";
$installer->run($query);
$installer->endSetup();
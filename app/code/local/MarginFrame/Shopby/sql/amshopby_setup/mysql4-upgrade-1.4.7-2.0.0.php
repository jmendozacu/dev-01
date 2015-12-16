<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|comment:1
 * @Migration field_exist:amshopby/filter|block_pos:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `comment` TEXT NOT NULL;
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `block_pos` VARCHAR(255) NOT NULL;
"); 

$this->endSetup();
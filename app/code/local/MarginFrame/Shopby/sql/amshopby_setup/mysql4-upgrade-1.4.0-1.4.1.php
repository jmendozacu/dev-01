<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/value|cms_block:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/value')}` ADD `cms_block` VARCHAR(255);
"); 

$this->endSetup();
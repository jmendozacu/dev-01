<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/value|featured_order:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/value')}` 
    ADD COLUMN `featured_order` TINYINT UNSIGNED DEFAULT 0
"); 
$this->endSetup();
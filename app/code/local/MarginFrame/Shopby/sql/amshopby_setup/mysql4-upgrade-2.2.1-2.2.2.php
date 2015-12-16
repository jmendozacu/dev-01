<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/value|img_small_hover:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/value')}` 
    ADD COLUMN `img_small_hover` VARCHAR(255) NOT NULL
"); 

$this->endSetup();
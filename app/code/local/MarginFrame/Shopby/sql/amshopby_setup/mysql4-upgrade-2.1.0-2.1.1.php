<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|depend_on_attribute:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD COLUMN `depend_on_attribute` VARCHAR(256) NOT NULL;
"); 

$this->endSetup();
<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|exclude_from:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `exclude_from` VARCHAR(255) NOT NULL;
"); 

$this->endSetup();
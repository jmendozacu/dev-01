<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|range:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD COLUMN `range` int NOT NULL;
"); 

$this->endSetup();
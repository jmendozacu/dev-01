<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|include_in:1
 */
$this->run("
ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD COLUMN `include_in` VARCHAR(256) NOT NULL;
");

$this->endSetup();
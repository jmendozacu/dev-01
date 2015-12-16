<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|max_options:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `max_options` SMALLINT NOT NULL AFTER `attribute_id` 
");

$this->endSetup();
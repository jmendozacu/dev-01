<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|use_and_logic:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `use_and_logic` TINYINT(1) NOT NULL DEFAULT 0;
");

$this->endSetup();

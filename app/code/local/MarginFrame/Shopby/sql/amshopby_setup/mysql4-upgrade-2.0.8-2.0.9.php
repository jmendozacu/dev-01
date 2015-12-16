<?php

/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/page|meta_kw:1
 * @Migration field_exist:amshopby/value|meta_kw:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/page')}` ADD COLUMN `meta_kw` varchar(255) NOT NULL;
    ALTER TABLE `{$this->getTable('amshopby/value')}` ADD COLUMN `meta_kw` varchar(255) NOT NULL;
");

$this->endSetup();
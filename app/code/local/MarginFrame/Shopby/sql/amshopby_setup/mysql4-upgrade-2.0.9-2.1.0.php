<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/page|cats:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/page')}` ADD COLUMN `cats` TEXT NOT NULL;
"); 

$this->endSetup();
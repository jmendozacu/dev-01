<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|show_search:1
 */
$this->run("

ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `show_search` TINYINT( 1 ) NOT NULL ,
ADD `slider_decimal` TINYINT( 1 ) NOT NULL ;


");
 
$this->endSetup();
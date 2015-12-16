<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/value|url_alias:1
 */
$this->run("
ALTER TABLE `{$this->getTable('amshopby/value')}` ADD  `url_alias` VARCHAR( 255 ) NULL DEFAULT NULL ,
ADD INDEX (  `url_alias` )
");
 
$this->endSetup();
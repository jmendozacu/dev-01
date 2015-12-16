<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Birth
 */  
$this->startSetup();

$this->run("
ALTER TABLE `{$this->getTable('ambirth/log')}` CHANGE `type` `type` VARCHAR( 32 ) NOT NULL; 
");

$this->endSetup(); 
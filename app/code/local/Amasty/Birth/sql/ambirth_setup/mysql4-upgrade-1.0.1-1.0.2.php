<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Birth
 */  
$this->startSetup();

$this->run("

ALTER TABLE `{$this->getTable('ambirth/log')}` ADD `type` ENUM( 'birth', 'reg' ) DEFAULT 'birth' NOT NULL AFTER `y` ;

");

$this->endSetup(); 
<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('career_application')} ADD `created_at` date NULL;
 
");

$installer->endSetup(); 
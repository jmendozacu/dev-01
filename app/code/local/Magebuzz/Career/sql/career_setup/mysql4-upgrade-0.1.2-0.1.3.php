<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('career_application')} ADD `application_for_job` text NULL DEFAULT '';
 
");

$installer->endSetup(); 
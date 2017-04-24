<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('flippingbook/magazine')} ADD `description` text NULL;

");

$installer->endSetup(); 
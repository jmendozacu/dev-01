<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('flippingbook/magazine')} ADD `url_direct_link` text NULL;

");

$installer->endSetup(); 
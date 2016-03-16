<?php
$installer = $this;
$installer->startSetup();

$installer->run("

	ALTER TABLE {$this->getTable('review/review')} ADD `img` VARCHAR(255) NULL;

	");

$installer->endSetup();
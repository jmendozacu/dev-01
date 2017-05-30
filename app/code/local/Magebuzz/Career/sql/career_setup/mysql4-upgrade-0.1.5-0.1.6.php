<?php

$installer = $this;
$installer->startSetup();

$installer->run("

ALTER TABLE `{$this->getTable('career_application')}`
  ADD COLUMN `attachment` varchar (255) NOT NULL default '' ;
");

$installer->endSetup();
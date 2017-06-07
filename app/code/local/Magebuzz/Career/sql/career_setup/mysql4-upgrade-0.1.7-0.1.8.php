<?php

$installer = $this;
$installer->startSetup();

$installer->run("

ALTER TABLE `{$this->getTable('career_application')}`
  MODIFY COLUMN `identity_number` varchar (255);
");

$installer->endSetup();
<?php
$installer = $this;
$installer->startSetup();

$installer->run("
  ALTER TABLE {$this->getTable('career_application')} MODIFY `date_of_birth` DATE;
");

$installer->endSetup();
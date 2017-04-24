<?php
$installer = $this;
$installer->startSetup();

$installer->run("
  ALTER TABLE {$this->getTable('career_job')} MODIFY `function` text;
  ALTER TABLE {$this->getTable('career_job')} MODIFY `position` int(11);
");

$installer->endSetup();
<?php
$installer = $this;
$installer->startSetup();
$installer->run("	
  ALTER TABLE {$this->getTable('faq')} CHANGE COLUMN `sort_order` `sort_order_faq`int(11) unsigned NOT NULL;
");
$installer->endSetup(); 
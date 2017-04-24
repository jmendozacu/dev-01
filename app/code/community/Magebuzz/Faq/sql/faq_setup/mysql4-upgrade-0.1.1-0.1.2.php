<?php
$installer = $this;
$installer->startSetup();
$installer->run("	
  ALTER TABLE {$this->getTable('faq')} ADD `sort_order` int(11) unsigned NOT NULL;
  ALTER TABLE {$this->getTable('faq_category')} ADD `sort_order` int(11) unsigned NOT NULL;
");
$installer->endSetup(); 
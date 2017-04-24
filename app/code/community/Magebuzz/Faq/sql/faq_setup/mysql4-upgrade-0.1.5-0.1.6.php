<?php
$installer = $this;
$installer->startSetup();
$installer->run("	
  ALTER TABLE {$this->getTable('faq_store')} CHANGE COLUMN `store_id` `store_id` smallint(5) unsigned NOT NULL;
   ALTER TABLE {$this->getTable('faq_store')} ADD CONSTRAINT `FK_FAQ_STORE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE
");
$installer->endSetup(); 
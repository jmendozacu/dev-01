<?php
$installer = $this;
$installer->startSetup();
$installer->run("
  ALTER TABLE {$this->getTable('faq_category')} ADD `category_group` varchar(255) NOT NULL default '';
  ");
$installer->endSetup();
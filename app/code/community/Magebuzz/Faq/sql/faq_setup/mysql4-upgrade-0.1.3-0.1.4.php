<?php
$installer = $this;

$installer->startSetup();
$installer->run("
  INSERT INTO  `{$this->getTable('faq_category_store')}` (category_id,store_id) VALUES (0,'0');
  INSERT INTO `{$this->getTable('faq_category')}` VALUES (0,'','1','','','uncategorized',99);
  INSERT INTO `{$this->getTable('core_url_rewrite')}` (store_id,id_path,request_path,target_path,is_system) VALUES (0,'faq/category/0','faq/category/uncategorized','faq/category/view/cid/0','1');
");
$installer->endSetup(); 
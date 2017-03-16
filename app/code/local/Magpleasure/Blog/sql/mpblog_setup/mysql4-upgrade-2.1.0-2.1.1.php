<?php
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE `{$this->getTable('mp_blog_categories')}`
  ADD COLUMN `category_is_landing_project` int(11) NULL default 0
");
$installer->endSetup();
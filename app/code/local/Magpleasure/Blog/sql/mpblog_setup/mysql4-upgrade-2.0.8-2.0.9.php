<?php
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE `{$this->getTable('mp_blog_categories')}`
  ADD COLUMN `category_style` VARCHAR(255) NOT NULL
");
$installer->endSetup();
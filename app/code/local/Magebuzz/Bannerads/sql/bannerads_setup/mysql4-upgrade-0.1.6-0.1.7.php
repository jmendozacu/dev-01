<?php

$installer = $this;

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->startSetup();
$setup->addAttribute('catalog_category', 'banner_ads_id', array(
	'type'             => 'int',
	'group'             => '',
	'backend'           => '',
	'frontend'          => '',
	'label'             => 'Banner Ads ID',
	'input'             => 'text',
	'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'visible'           => true,
	'required'          => false,
	'user_defined'      => false,
	'default'           => '0',
	'searchable'        => false,
	'filterable'        => false,
	'comparable'        => false,
	'visible_on_front'  => false,
	'unique'            => false,
	'group'             => 'General Information',    ));
$installer->endSetup(); 
<?php

$installer = $this;

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->startSetup();

$setup->addAttribute('catalog_category', 'category_mapping', array(
	'input'         => 'text',
    'type'          => 'varchar',
	'group'             => '',
	'backend'           => '',
	'frontend'          => '',
	'label'             => 'Category Mapping',
	//'input'             => 'text',
	'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	'visible'           => true,
	'required'          => false,
	'user_defined'      => false,
	'default'           => '',
	'searchable'        => false,
	'filterable'        => false,
	'comparable'        => false,
	'visible_on_front'  => false,
	'unique'            => false,
	'group'             => 'General Information',    ));

$installer->endSetup(); 
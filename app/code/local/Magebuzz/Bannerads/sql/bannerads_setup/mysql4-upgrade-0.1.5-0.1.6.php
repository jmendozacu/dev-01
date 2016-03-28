<?php
	$installer = $this;
	$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
	$installer->startSetup();

	$setup->addAttribute('catalog_category', 'category_icon', array(
			'type'             	=> 'varchar',
			'group'             => '',
			'backend' 					=> 'catalog/category_attribute_backend_image',
			'frontend'          => '',
			'label'             => 'Icon',
			'input'             => 'image',
			'class'             => '',
			'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			'visible'           => true,
			'required'          => false,
			'user_defined'      => false,
			'default'           => '0',
			'searchable'        => false,
			'filterable'        => true,
			'comparable'        => false,
			'visible_on_front'  => true,
			'unique'            => false,
			'group'             => 'General Information',    ));
	$installer->endSetup();
?>
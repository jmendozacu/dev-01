<?php

$installer = $this;

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->startSetup();
$setup->addAttribute('catalog_category', 'display_from_date', array(
  'input'             => 'datetime',
  'type'              => 'datetime',
  'backend'           => 'bannerads/attribute_backend_startdate',
  'frontend'          => '',
  'label'             => 'Display on frontend from date',
  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
  'visible'           => true,
  'required'          => false,
  'user_defined'      => false,
  'default'           => null,
  'searchable'        => false,
  'filterable'        => false,
  'comparable'        => false,
  'visible_on_front'  => false,
  'unique'            => false,
  'group'             => 'Display Settings',
));

$setup->addAttribute('catalog_category', 'display_to_date', array(
  'input'             => 'datetime',
  'type'              => 'datetime',
  'backend'           => 'bannerads/attribute_backend_startdate',
  'frontend'          => '',
  'label'             => 'Display on frontend to date',
  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
  'visible'           => true,
  'required'          => false,
  'user_defined'      => false,
  'default'           => null,
  'searchable'        => false,
  'filterable'        => false,
  'comparable'        => false,
  'visible_on_front'  => false,
  'unique'            => false,
  'group'             => 'Display Settings',
));
$installer->endSetup(); 
<?php

$installer = new Mage_Customer_Model_Entity_Setup('core_setup');
$installer->startSetup();

//Telephone Ext.
$installer->addAttribute('customer_address', 'telephone_ext', array(
        'label'	=> 'Telephone Ext.',
        'type'      => 'varchar',
        'input'     => 'text',
        'visible'   => true,
        'required'  => false,
        'unique'    => true,
        'position'  => 121,
        ));

$eavConfig = Mage::getSingleton('eav/config');
$attribute = $eavConfig->getAttribute('customer_address', 'telephone_ext');
$attribute->setData('used_in_forms', array('customer_address_edit',
                                           'customer_register_address',
                                           'adminhtml_customer_address'));
$attribute->save();


//Mobile Number
$installer->addAttribute('customer_address', 'mobile_no', array(
        'label'	=> 'Mobile Number',
        'type'      => 'varchar',
        'input'     => 'text',
        'visible'   => true,
        'required'  => false,
        'unique'    => true,
        'position'  => 122,
        ));

$eavConfig = Mage::getSingleton('eav/config');
$attribute = $eavConfig->getAttribute('customer_address', 'mobile_no');
$attribute->setData('used_in_forms', array('customer_address_edit',
                                           'customer_register_address',
                                           'adminhtml_customer_address'));
$attribute->save();


$installer->endSetup();
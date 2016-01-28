<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Dealerlocator_Block_Adminhtml_Dealerlocator_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
  protected function _prepareForm() {
    $form = new Varien_Data_Form();
    $this->setForm($form);
    $fieldset = $form->addFieldset('dealerlocator_form', array('legend' => Mage::helper('dealerlocator')->__('Dealer information')));

    $model = Mage::registry('dealerlocator_data');
    $dealTag = $model->getDealerTag();
    $model->setDealerTag(implode(',', $dealTag));

    if (Mage::getSingleton('adminhtml/session')->getDealerlocatorData()) {
      $data = Mage::getSingleton('adminhtml/session')->getDealerlocatorData();
      Mage::getSingleton('adminhtml/session')->setDealerlocatorData(null);
    } elseif (Mage::registry('dealerlocator_data')) {
      $data = Mage::registry('dealerlocator_data')->getData();
    }

    $fieldset->addField('title', 'text', array('label' => Mage::helper('dealerlocator')->__('Title'), 'class' => 'required-entry', 'required' => TRUE, 'name' => 'title',));

    $fieldset->addField('email', 'text', array('label' => Mage::helper('dealerlocator')->__('Email'), 'name' => 'email',));

    $fieldset->addField('website', 'text', array('label' => Mage::helper('dealerlocator')->__('Website'), 'name' => 'website',));

    $fieldset->addField('phone', 'text', array('label' => Mage::helper('dealerlocator')->__('Phone'), 'required' => FALSE, 'name' => 'phone',));

    $fieldset->addField('postal_code', 'text', array('label' => Mage::helper('dealerlocator')->__('Postal Code'), 'class' => 'required-entry', 'required' => TRUE, 'name' => 'postal_code',));

    $fieldset->addField('address', 'textarea', array('label' => Mage::helper('dealerlocator')->__('Address'), 'class' => 'required-entry', 'required' => TRUE, 'name' => 'address', 'after_element_html' => Mage::helper('dealerlocator')->__('<small>Leave 2 fields below empty if you do NOT know exact values.</small>')));

    $fieldset->addField('longitude', 'text', array('label' => Mage::helper('dealerlocator')->__('Longitude'), 'name' => 'longitude',));

    $fieldset->addField('latitude', 'text', array('label' => Mage::helper('dealerlocator')->__('Latitude'), 'name' => 'latitude',));

    $fieldset->addField('icon_image', 'image', array('label' => Mage::helper('dealerlocator')->__('Dealer Icon'), 'required' => FALSE, 'name' => 'icon_image',));

    $fieldset->addField('dealer_tag', 'text', array('label' => Mage::helper('dealerlocator')->__('Dealer Tag'), 'required' => FALSE, 'name' => 'dealer_tag', 'after_element_html' => Mage::helper('dealerlocator')->__('<small>Example : magebuzz,MageBuzz,Azebiz</small>')));

    if (!Mage::app()->isSingleStoreMode()) {
      $fieldset->addField('store_id', 'multiselect', array('name' => 'stores[]', 'label' => Mage::helper('dealerlocator')->__('Store View'), 'title' => Mage::helper('dealerlocator')->__('Store View'), 'required' => TRUE, 'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(FALSE, TRUE),));
    } else {
      $fieldset->addField('store_id', 'hidden', array('name' => 'stores[]', 'value' => Mage::app()->getStore(TRUE)->getId()));
      $model->setStoreId(Mage::app()->getStore(TRUE)->getId());
    }

    $fieldset->addField('status', 'select', array('label' => Mage::helper('dealerlocator')->__('Status'), 'name' => 'status', 'values' => array(array('value' => 1, 'label' => Mage::helper('dealerlocator')->__('Enabled'),), array('value' => 2, 'label' => Mage::helper('dealerlocator')->__('Disabled'),),),));

    $fieldset->addField('note', 'textarea', array('label' => Mage::helper('dealerlocator')->__('Note'), 'name' => 'note', 'after_element_html' => Mage::helper('dealerlocator')->__('<small>Instruction for this dealer store</small>')));

    if (isset($data['icon_image']) && $data['icon_image'] != '') {
      $data['icon_image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'dealers/icons/' . $data['icon_image'];
    }

    $form->setValues($data);
    return parent::_prepareForm();
  }
}
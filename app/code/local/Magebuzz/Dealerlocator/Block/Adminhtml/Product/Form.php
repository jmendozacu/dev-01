<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Dealerlocator_Block_Adminhtml_Product_Form extends Mage_Adminhtml_Block_Widget_Form {
  protected function _prepareForm() {
    $sampleCSVpath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'dealerlocator/product_dealer.csv ';
    $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getUrl('*/adminhtml_product/save'), 'method' => 'post', 'enctype' => 'multipart/form-data'));
    $fieldset = $form->addFieldset('edit_form', array('legend' => Mage::helper('dealerlocator')->__('Add product via CSV file')));
    $fieldset->addField('csv_file', 'file', array('name' => 'csv_file', 'label' => Mage::helper('dealerlocator')->__('Choose CSV file to import'), 'after_element_html' => Mage::helper('dealerlocator')->__('<br/>A CSV file may contain many dealers (<a href="%s">Sample CSV file</a>)', $sampleCSVpath)));
    $form->setUseContainer(TRUE);
    $this->setForm($form);;

    return parent::_prepareForm();
  }
}
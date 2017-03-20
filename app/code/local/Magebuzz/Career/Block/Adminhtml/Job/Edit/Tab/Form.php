<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Career_Block_Adminhtml_Job_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
  protected function _prepareForm() {
    $form = new Varien_Data_Form();
    $this->setForm($form);
    $fieldset = $form->addFieldset('job_form', array('legend' => Mage::helper('career')->__('Job information')));

    if (Mage::getSingleton('adminhtml/session')->getCategoryData()) {
      $data = Mage::getSingleton('adminhtml/session')->getCategoryData();
      Mage::getSingleton('adminhtml/session')->setCategoryData(null);
    } elseif (Mage::registry('job_current_submit')) {
      $data = Mage::registry('job_current_submit')->getData();
    }

    $fieldset->addField(
      'title',
      'text',
      array('label' => Mage::helper('career')->__('Title'),
        'name' => 'title',)
    );
    $fieldset->addField(
      'function',
      'text',
      array('label' => Mage::helper('career')->__('Function'),
        'name' => 'function',)
    );
    $fieldset->addField(
      'position',
      'text',
      array('label' => Mage::helper('career')->__('Position'),
        'name' => 'position',)
    );
    $fieldset->addField(
      'location',
      'text',
      array('label' => Mage::helper('career')->__('Location'),
        'name' => 'location',)
    );
    $fieldset->addField(
      'scope_of_work',
      'textarea',
      array('label' => Mage::helper('career')->__('Scope Of Work'),
        'name' => 'scope_of_work',)
    );
    $fieldset->addField(
      'qualifications',
      'textarea',
      array('label' => Mage::helper('career')->__('Qualifications'),
        'name' => 'qualifications',)
    );
    $fieldset->addField(
      'status',
      'select',
      array('label' => Mage::helper('career')->__('Status'), 'name' => 'status',
        'values' => array(array('value' => 1, 'label' => Mage::helper('career')->__('Enabled'),),

      array('value' => 0, 'label' => Mage::helper('career')->__('Disabled'),),),));


    $form->setValues($data);
    return parent::_prepareForm();
  }
}

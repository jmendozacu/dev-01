<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Career_Block_Adminhtml_Worktype_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
  protected function _prepareForm() {
    $form = new Varien_Data_Form();
    $this->setForm($form);
    $fieldset = $form->addFieldset('worktype_form', array('legend' => Mage::helper('career')->__('Work Type information')));

    if (Mage::getSingleton('adminhtml/session')->getCategoryData()) {
      $data = Mage::getSingleton('adminhtml/session')->getCategoryData();
      Mage::getSingleton('adminhtml/session')->setCategoryData(null);
    } elseif (Mage::registry('worktype_current_submit')) {
      $data = Mage::registry('worktype_current_submit')->getData();
    }

    $fieldset->addField(
      'title',
      'text',
      array('label' => Mage::helper('career')->__('Title'),
        'name' => 'title',
        'class'     => 'required-entry',
        'required'  => true,)
    );

    $field = $fieldset->addField(
        'store_id',
      'multiselect',
      array('name' => 'store_id[]',
        'label' => Mage::helper('career')->__('Store View'),
        'title' => Mage::helper('career')->__('Store View'),
        'required' => TRUE,
        'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(FALSE, TRUE),
        )
    );
    $renderer = $this->getLayout()
      ->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
    $field->setRenderer($renderer);

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

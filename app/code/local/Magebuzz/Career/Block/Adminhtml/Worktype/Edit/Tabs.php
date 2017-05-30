<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Career_Block_Adminhtml_Worktype_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
  public function __construct() {
    parent::__construct();
    $this->setId('worktype_tabs');
    $this->setDestElementId('edit_form');
    $this->setTitle(Mage::helper('career')->__('Work Type Information'));
  }

  protected function _beforeToHtml() {
    $this->addTab('form_section', array('label' => Mage::helper('career')->__('Work Type Information'), 'title' => Mage::helper('career')->__('Work Type Information'), 'content' => $this->getLayout()->createBlock('career/adminhtml_worktype_edit_tab_form')->toHtml(),));

    return parent::_beforeToHtml();
  }
}
<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Career_Block_Adminhtml_Job_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
  public function __construct() {
    parent::__construct();
    $this->setId('job_tabs');
    $this->setDestElementId('edit_form');
    $this->setTitle(Mage::helper('career')->__('Job Information'));
  }

  protected function _beforeToHtml() {
    $this->addTab('form_section', array('label' => Mage::helper('career')->__('Job Information'), 'title' => Mage::helper('career')->__('Job Information'), 'content' => $this->getLayout()->createBlock('career/adminhtml_job_edit_tab_form')->toHtml(),));

    return parent::_beforeToHtml();
  }
}
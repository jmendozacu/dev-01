<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Career_Block_Adminhtml_Job_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
  public function __construct() {
    parent::__construct();
    $this->_objectId = 'job';
    $this->_blockGroup = 'career';
    $this->_controller = 'adminhtml_job';

    $this->_updateButton('save', 'label', Mage::helper('career')->__('Save Job'));
    $this->_updateButton('delete', 'label', Mage::helper('career')->__('Delete Job'));

    $this->_addButton('saveandcontinue', array('label' => Mage::helper('adminhtml')->__('Save And Continue Edit'), 'onclick' => 'saveAndContinueEdit()', 'class' => 'save',), -100);

    $this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('categories_content') == null) {
					tinyMCE.execCommand('mceAddControl', false, 'categories_content');
				} else {
					tinyMCE.execCommand('mceRemoveControl', false, 'categories_content');
				}
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
  }

  public function getHeaderText() {
    if (Mage::registry('job_current_submit') && Mage::registry('job_current_submit')->getId()) {
      return Mage::helper('career')->__("Edit Job '%s'", $this->htmlEscape(Mage::registry('job_current_submit')->getTitle()));
    } else {
      return Mage::helper('career')->__('Add Job');
    }
  }

  /*
  * set Load TinyMce to use Wysiwyg editor
  */
  protected function _prepareLayout() {
    parent::_prepareLayout();
    if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
      $this->getLayout()->getBlock('head')->setCanLoadTinyMce(TRUE);
    }
  }
}

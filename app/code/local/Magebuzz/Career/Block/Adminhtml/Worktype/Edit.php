<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Career_Block_Adminhtml_Worktype_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
  public function __construct() {
    parent::__construct();
    $this->_objectId = 'worktype';
    $this->_blockGroup = 'career';
    $this->_controller = 'adminhtml_worktype';

    $this->_updateButton('save', 'label', Mage::helper('career')->__('Save Work Type'));
    $this->_updateButton('delete', 'label', Mage::helper('career')->__('Delete Work Type'));

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
    if (Mage::registry('worktype_current_submit') && Mage::registry('worktype_current_submit')->getId()) {
      return Mage::helper('career')->__("Edit Work Type '%s'", $this->htmlEscape(Mage::registry('worktype_current_submit')->getTitle()));
    } else {
      return Mage::helper('career')->__('Add Work Type');
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

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Page_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
   
    public function __construct()
    {
        $this->_objectId = 'page_id';
        $this->_blockGroup = 'flippingbook';
        $this->_controller = 'adminhtml_page';

        parent::__construct();

        $this->_removeButton('reset');
        $this->_updateButton('save', 'label', $this->__('Save Page'));
        $this->_updateButton('delete', 'label', $this->__('Delete Page'));

        $this->_addButton('saveandcontinue',
            array(
                'label'		=> $this->__('Save and continue edit'),
                'onclick'	=> 'saveAndContinueEdit()',
                'class'		=> 'save'
            ),
            -100
        );

        $this->_formScripts[] = "
			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
    }

    public function getHeaderText()
    {
        if (Mage::registry('flippingbook_page')->getId()) {
            return $this->__("Edit Page '%s'", $this->htmlEscape(Mage::registry('flippingbook_page')->getName()));
        } else {
            return $this->__('New Page');
        }
    }

    public function getHeaderCssClass()
    {
        return '';
    }

}

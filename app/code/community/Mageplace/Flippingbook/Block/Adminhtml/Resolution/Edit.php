<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Resolution_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId   = 'resolution_id';
        $this->_blockGroup = 'flippingbook';
        $this->_controller = 'adminhtml_resolution';

        parent::__construct();

        $this->_removeButton('reset');
        $this->_updateButton('save', 'label', $this->__('Save Resolution'));
        $this->_updateButton('delete', 'label', $this->__('Delete Resolution'));

        $this->_addButton('saveandcontinue',
            array(
                'label'   => $this->__('Save and continue edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save'
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
        if (Mage::registry('flippingbook_resolution')->getId()) {
            return $this->__("Edit Resolution '%s'", $this->htmlEscape(Mage::registry('flippingbook_resolution')->getName()));
        } else {
            return $this->__('New Resolution');
        }
    }

    public function getHeaderCssClass()
    {
        return '';
    }
}
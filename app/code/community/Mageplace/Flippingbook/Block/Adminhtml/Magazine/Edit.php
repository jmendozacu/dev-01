<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Magazine_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId   = 'magazine_id';
        $this->_blockGroup = 'flippingbook';
        $this->_controller = 'adminhtml_magazine';

        parent::__construct();

        $this->_removeButton('reset');
        $this->_updateButton('save', 'label', $this->__('Save Book'));
        $this->_updateButton('delete', 'label', $this->__('Delete Book'));

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
        if (Mage::registry('flippingbook_magazine')->getId()) {
            return $this->__("Edit Book '%s'", $this->htmlEscape(Mage::registry('flippingbook_magazine')->getName()));
        } else {
            return $this->__('New Book');
        }
    }

    public function getHeaderCssClass()
    {
        return '';
    }

}

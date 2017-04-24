<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Template_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId   = 'template_id';
        $this->_blockGroup = 'flippingbook';
        $this->_controller = 'adminhtml_template';

        parent::__construct();

        $this->_removeButton('reset');
        $this->_updateButton('save', 'label', $this->__('Save Template'));
        $this->_updateButton('delete', 'label', $this->__('Delete Template'));

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
        if (Mage::registry('flippingbook_template')->getId()) {
            return $this->__("Edit Template '%s'", $this->htmlEscape(Mage::registry('flippingbook_template')->getName()));
        } else {
            return $this->__('New Template');
        }
    }

    public function getHeaderCssClass()
    {
        return '';
    }

}
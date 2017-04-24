<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Template_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->addItem('js_css', 'flippingbook/colorpicker/colorPicker.css')
                ->addJs('scriptaculous/scriptaculous.js')
                ->addJs('flippingbook/colorpicker/yahoo.color.js')
                ->addJs('flippingbook/colorpicker/colorPicker.js');
        }

        return parent::_prepareLayout();
    }

    public function __construct()
    {
        parent::__construct();
        $this->setId('template_id');
        $this->setTitle(Mage::helper('flippingbook')->__('Template Information'));
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getUrl('*/*/save', array('template_id' => $this->getRequest()->getParam('template_id'))), 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }


}
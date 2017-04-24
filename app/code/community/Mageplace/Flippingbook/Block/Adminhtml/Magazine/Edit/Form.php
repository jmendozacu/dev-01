<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Magazine_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        if (($headBlock = $this->getLayout()->getBlock('head')) && (Mage::getSingleton('cms/wysiwyg_config')->isEnabled())) {
            $headBlock->setCanLoadTinyMce(true);
        }
        return parent::_prepareLayout();
    }


    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setId('edit_form');
        $form->setAction($this->getSaveUrl());
        $form->setMethod('post');
        $form->setEnctype('multipart/form-data');
        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }
}

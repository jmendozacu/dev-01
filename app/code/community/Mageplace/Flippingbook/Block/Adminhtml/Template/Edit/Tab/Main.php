<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Template_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('flippingbook_template');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('template_');

        $fieldset = $form->addFieldset('main_fieldset', array('legend' => Mage::helper('flippingbook')->__('Template Details')));

        if ($model->getTemplateId()) {
            $fieldset->addField('template_id', 'hidden', array(
                'name' => 'template_id',
            ));
        }

        $fieldset->addField('template_name', 'text', array(
            'name'     => 'template_name',
            'label'    => Mage::helper('flippingbook')->__('Template Name'),
            'title'    => Mage::helper('flippingbook')->__('Template Name'),
            'required' => true,
        ));

        $fieldset->addField('root_template', 'select', array(
            'name'     => 'root_template',
            'label'    => Mage::helper('flippingbook')->__('Layout'),
            'title'    => Mage::helper('flippingbook')->__('Layout'),
            'values'   => Mage::getSingleton('page/source_layout')->toOptionArray(),
            'required' => true,
            'note'     => $this->__('Will be used only for no popup mode.')
        ));


        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return Mage::helper('flippingbook')->__('Template Details');
    }

    public function getTabTitle()
    {
        return Mage::helper('flippingbook')->__('Template Details');
    }


    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

}
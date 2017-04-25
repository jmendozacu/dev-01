<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Template_Edit_Tab_Elements
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('flippingbook_template');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('template_');

        $fieldset = $form->addFieldset('elements_fieldset', array('legend' => Mage::helper('flippingbook')->__('Display elements')));

        $fieldset->addField('display_slider', 'select', array(
            'label'              => Mage::helper('flippingbook')->__('Display Slider'),
            'title'              => Mage::helper('flippingbook')->__('Display Slider'),
            'after_element_html' => Mage::helper('flippingbook')->__('Display Slider with pages numbers'),
            'name'               => 'display_slider',
            'values'             => Mage::getModel('eav/entity_attribute_source_boolean')->getAllOptions()
        ));

        $fieldset->addField('display_pagebox', 'select', array(
            'label'              => Mage::helper('flippingbook')->__('Display Pagebox'),
            'title'              => Mage::helper('flippingbook')->__('Display Pagebox'),
            'name'               => 'display_pagebox',
            'values'             => Mage::getModel('eav/entity_attribute_source_boolean')->getAllOptions()
        ));
        $fieldset->addField('display_title', 'select', array(
            'label'              => Mage::helper('flippingbook')->__('Display Title'),
            'title'              => Mage::helper('flippingbook')->__('Display Title'),
            'after_element_html' => Mage::helper('flippingbook')->__('Display publication title'),
            'name'               => 'display_title',
            'values'             => Mage::getModel('eav/entity_attribute_source_boolean')->getAllOptions()
        ));
        $fieldset->addField('display_top_icons', 'select', array(
            'label'              => Mage::helper('flippingbook')->__('Display Top Icons'),
            'title'              => Mage::helper('flippingbook')->__('Display Top Icons'),
            'after_element_html' => Mage::helper('flippingbook')->__('Display social and contents icons ( if enabled in publication )'),
            'name'               => 'display_top_icons',
            'values'             => Mage::getModel('eav/entity_attribute_source_boolean')->getAllOptions()
        ));
        $fieldset->addField('display_move_button', 'select', array(
            'label'              => Mage::helper('flippingbook')->__('Display Next&Prev Buttons'),
            'title'              => Mage::helper('flippingbook')->__('Display Next&Prev Buttons'),
            'name'               => 'display_move_button',
            'values'             => Mage::getModel('eav/entity_attribute_source_boolean')->getAllOptions()
        ));


        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return Mage::helper('flippingbook')->__('Display elements');
    }

    public function getTabTitle()
    {
        return Mage::helper('flippingbook')->__('Display elements');
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
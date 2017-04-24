<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Template_Edit_Tab_Page
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('flippingbook_template');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('template_');

        $fieldset = $form->addFieldset('page_fieldset', array('legend' => Mage::helper('flippingbook')->__('Page Style')));

        $fieldset->addField('font_family', 'select', array(
            'label'              => Mage::helper('flippingbook')->__('Font Family'),
            'title'              => Mage::helper('flippingbook')->__('Font Family'),
            'name'               => 'font_family',
            'after_element_html' => '(font-family)',
            'values'             => Mage::getModel('flippingbook/adminhtml_system_config_source_template_fontfamily')->toOptionArray()
        ));

        $fieldset->addField('font_size', 'select', array(
            'label'              => Mage::helper('flippingbook')->__('Font Size'),
            'title'              => Mage::helper('flippingbook')->__('Font Size'),
            'name'               => 'font_size',
            'after_element_html' => '(font-size)',
            'values'             => Mage::getModel('flippingbook/adminhtml_system_config_source_template_fontsize')->toOptionArray()
        ));

        $fieldset->addField('paragraph_spacing', 'select', array(
            'label'              => Mage::helper('flippingbook')->__('Paragraph Spacing'),
            'title'              => Mage::helper('flippingbook')->__('Paragraph Spacing'),
            'after_element_html' => '(line-height)',
            'name'               => 'paragraph_spacing',
            'values'             => Mage::getModel('flippingbook/adminhtml_system_config_source_template_paragraphSpasing')->toOptionArray()
        ));

        $fieldset->addField('line_spacing', 'select', array(
            'label'              => Mage::helper('flippingbook')->__('Line Spacing'),
            'title'              => Mage::helper('flippingbook')->__('Line Spacing'),
            'after_element_html' => '(margin)',
            'name'               => 'line_spacing',
            'values'             => Mage::getModel('flippingbook/adminhtml_system_config_source_template_lineSpacing')->toOptionArray()
        ));

        $fieldset->addType('colorpicker', Mage::getConfig()->getBlockClassName('flippingbook/form_element_colorpicker'));

        $fieldset->addField('page_background_color', 'colorpicker', array(
            'label' => Mage::helper('flippingbook')->__('Page Background Color'),
            'title' => Mage::helper('flippingbook')->__('Page Background Color'),
            'name'  => 'page_background_color'
        ));


        $fieldset->addField('background_color', 'colorpicker', array(
            'label' => Mage::helper('flippingbook')->__('Background Color'),
            'title' => Mage::helper('flippingbook')->__('Background Color'),
            'name'  => 'background_color'
        ));

        $fieldset->addField('text_color', 'colorpicker', array(
            'label' => Mage::helper('flippingbook')->__('Text Color'),
            'title' => Mage::helper('flippingbook')->__('Text Color'),
            'name'  => 'text_color'
        ));


        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return Mage::helper('flippingbook')->__('Page Style');
    }

    public function getTabTitle()
    {
        return Mage::helper('flippingbook')->__('Page Style');
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
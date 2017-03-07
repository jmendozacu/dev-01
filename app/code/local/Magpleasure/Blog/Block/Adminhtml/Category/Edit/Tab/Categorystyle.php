<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Magpleasure_Blog
 */

class Magpleasure_Blog_Block_Adminhtml_Category_Edit_Tab_Categorystyle extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Helper
     * @return Magpleasure_Blog_Helper_Data
     */
    protected function _helper() {
        return Mage::helper('mpblog');
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('blog_form', array('legend' => $this->_helper()->__('Category Style')));

        $fieldset->addField('category_style', 'select', array(
            'label' => $this->_helper()->__('Category Style'),
            'class' => 'required-entry',
            'required' => false,
            'name' => 'category_style',
            'values' => Mage::getModel('mpblog/categorystyle')->getOptionArray(),
        ));

        if (Mage::getSingleton('adminhtml/session')->getPostData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPostData());
            Mage::getSingleton('adminhtml/session')->getPostData(null);
        } elseif (Mage::registry('current_category')) {
            $form->setValues(Mage::registry('current_category')->getData());
        }

        return parent::_prepareForm();
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel() {
        return $this->_helper()->__("Category Style");
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle() {
        return $this->_helper()->__("Category Style");
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab() {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden() {
        return false;
    }
}
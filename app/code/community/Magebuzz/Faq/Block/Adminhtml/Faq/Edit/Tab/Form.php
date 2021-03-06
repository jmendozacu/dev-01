<?php

class Magebuzz_Faq_Block_Adminhtml_Faq_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _helper()
    {
        return Mage::helper('faq');
    }
    public function getTabLabel()
    {
        return $this->_helper()->__("Answer");
    }
    public function getTabTitle()
    {
        return $this->_helper()->__("Content");
    }
    public function canShowTab()
    {
        return true;
    }
    public function isHidden()
    {
        return false;
    }
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('faq_form', array('legend' => Mage::helper('faq')->__('General Information')));
        $fieldset->addType('mp_full_editor', 'Magpleasure_Blog_Block_Adminhtml_Widget_Form_Wysiwyg');

        $model = Mage::registry('faq_data');
        $fieldset->addField('question', 'text', array(
            'name' => 'question',
            'label' => Mage::helper('faq')->__('Question'),
            'title' => Mage::helper('faq')->__('Question'),
            'required' => true,
        ));

        $fieldset->addField('is_active', 'select', array(
            'label' => Mage::helper('faq')->__('Active'),
            'name' => 'is_active',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('faq')->__('Enabled'),
                ),

                array(
                    'value' => 0,
                    'label' => Mage::helper('faq')->__('Disabled'),
                ),
            ),
        ));

        $fieldset->addField('category_id', 'select', array(
            'name' => 'categories[]',
            'label' => Mage::helper('faq')->__('Category'),
            'title' => Mage::helper('faq')->__('Category'),
            'required' => false,
            'values' => Mage::getResourceSingleton('faq/category_collection')->toOptionArray(),
        ));
        $fieldset->addField('sort_order_faq', 'text', array(
            'name' => 'sort_order_faq',
            'label' => Mage::helper('faq')->__('Sort Order'),
            'title' => Mage::helper('faq')->__('Sort Order'),
            'required' => false,
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('cms')->__('Store View'),
                'title' => Mage::helper('cms')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name' => 'stores[]',
                'value' => Mage::app()->getStore(true)->getId()
            ));
            Mage::registry('faq_data')->setStoreId(Mage::app()->getStore(true)->getId());
        }


        try {
            $editorConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
                array('tab_id' => $this->getTabId())
            );
            $editorConfig->setData(Mage::helper('faq')->recursiveReplace(
                '/faq/',
                '/' . (string)Mage::app()->getConfig()->getNode('admin/routers/adminhtml/args/frontName') . '/',
                $editorConfig->getData()
            )
            );

        } catch (Exception $ex) {
            $editorConfig = array();
        }

        $fieldset->addField('answer', 'mp_full_editor', array(
          'name'      => 'answer',
          'label'     => Mage::helper('faq')->__('Answer'),
          'title'     => Mage::helper('faq')->__('Answer'),
          'style'     => 'min-width: 36em; width: 100%;',
          'required'  => true,
          'min_height'=> 300,
        ));

        if (Mage::getSingleton('adminhtml/session')->getFaqData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getFaqData());
            Mage::getSingleton('adminhtml/session')->setFaqData(null);
        } elseif (Mage::registry('faq_data')) {
            $form->setValues(Mage::registry('faq_data')->getData());
        }
        return parent::_prepareForm();
    }
}
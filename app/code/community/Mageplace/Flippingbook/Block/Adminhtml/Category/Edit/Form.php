<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Category_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('flippingbook_category');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset',
            array(
                'legend' => $this->__('Category Details'),
                'class'  => 'fieldset-wide'
            )
        );

        if ($model->getId()) {
            $fieldset->addField('category_id',
                'hidden',
                array(
                    'name' => 'category_id'
                )
            );
        }

        $fieldset->addField('category_name',
            'text',
            array(
                'name'     => 'category_name',
                'label'    => $this->__('Category Name'),
                'title'    => $this->__('Category Name'),
                'required' => true,
            )
        );

        $fieldset->addField('category_description',
            'textarea',
            array(
                'name'  => 'category_description',
                'label' => $this->__('Category Description'),
                'title' => $this->__('Category Description'),
            )
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $form->setAction($this->getSaveUrl());
        $form->setId('edit_form');
        $form->setMethod('post');

        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }
}

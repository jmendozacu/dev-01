<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Resolution_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('flippingbook_resolution');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset',
            array(
                'legend'	=> $this->__('Resolution Details'),
                'class'		=> 'fieldset-wide'
            )
        );

        if ($model->getId()) {
            $fieldset->addField('resolution_id',
                'hidden',
                array(
                    'name' => 'resolution_id'
                )
            );
        }

        $fieldset->addField('resolution_name',
            'text',
            array(
                'name'		=> 'resolution_name',
                'label'		=> $this->__('Resolution Name'),
                'title'		=> $this->__('Resolution Name'),
                'required'	=> true,
            )
        );

        $fieldset->addField('resolution_width',
            'text',
            array(
                'name'		=> 'resolution_width',
                'label'		=> $this->__('Resolution Width'),
                'title'		=> $this->__('Resolution Width'),
                'class'		=> 'validate-digits',
                'style'		=> 'width:50px!important;',
                'required'	=> true,
            )
        );

        $fieldset->addField('resolution_height',
            'text',
            array(
                'name'		=> 'resolution_height',
                'label'		=> $this->__('Resolution Height'),
                'title'		=> $this->__('Resolution Height'),
                'class'		=> 'validate-digits',
                'style'		=> 'width:50px!important;',
                'required'	=> true,
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

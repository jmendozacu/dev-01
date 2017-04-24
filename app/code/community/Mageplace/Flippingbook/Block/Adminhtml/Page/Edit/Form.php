<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Page_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
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
        $model          = Mage::registry('flippingbook_page');
        $magazine_model = Mage::registry('flippingbook_magazine');

        $form = new Varien_Data_Form();

        $fieldset_details = $form->addFieldset('base_fieldset',
            array(
                'legend' => $this->__('Page Details'),
                'class'  => 'fieldset-wide'
            )
        );

        if (!$model->getId()) {
            $fieldset_details->addField('page_magazine_id',
                'select',
                array(
                    'name'     => 'page_magazine_id',
                    'label'    => $this->__('Page Book'),
                    'title'    => $this->__('Page Book'),
                    'required' => true,
                    'values'   => $this->_getMagazinesValuesForForm()
                )
            );
        } else {
            $fieldset_details->addField('page_magazine_title',
                'note',
                array(
                    'text'  => '<h3>' . $magazine_model->getName() . '<h3>',
                    'label' => $this->__('Page Book'),
                )
            );

            $fieldset_details->addField('page_id',
                'hidden',
                array(
                    'name' => 'page_id'
                )
            );

            $fieldset_details->addField('page_magazine_id',
                'hidden',
                array(
                    'name' => 'page_magazine_id'
                )
            );
        }

        $fieldset_details->addField('page_title',
            'text',
            array(
                'name'     => 'page_title',
                'label'    => $this->__('Page Title'),
                'title'    => $this->__('Page Title'),
                'required' => true,
            )
        );


        $page_type = $fieldset_details->addField('page_type',
            'select',
            array(
                'name'     => 'page_type',
                'label'    => $this->__('Page Content'),
                'title'    => $this->__('Page Content'),
                'required' => true,
                'options'  => array(
                    'Text'  => $this->__('Text'),
                    'Image' => $this->__('Image')

                )
            )
        );


        $page_image = $fieldset_details->addField('page_image',
            'image',
            array(
                'name'  => 'page_image',
                'label' => $this->__('Page image'),
                'note'  => $this->__('Select gif, jpg or png files')
            )
        );
        if ($model->getData('page_image')) {
            $model->setData('page_image', Mage::helper('flippingbook')->getPathUrl('page') . '/' . $model->getData('page_image'));
        }

        $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        $config->setHeight('500px');
        $page_text = $fieldset_details->addField('page_text',
            'editor',
            array(
                'name'   => 'page_text',
                'label'  => $this->__('Page text'),
                'title'  => $this->__('Page text'),
                'config' => $config,

            )
        );
        $fieldset_details->addField('is_active',
            'select',
            array(
                'name'     => 'is_active',
                'label'    => $this->__('Page Status'),
                'title'    => $this->__('Page Status'),
                'required' => true,
                'options'  => array(
                    1 => Mage::helper('cms')->__('Enabled'),
                    0 => Mage::helper('cms')->__('Disabled')
                )
            )
        );

        $fieldset_details->addField('page_sort_order',
            'text',
            array(
                'name'  => 'page_sort_order',
                'label' => $this->__('Page Position'),
                'title' => $this->__('Page Position'),
                'class' => 'validate-digits',
                'style' => 'width: 30px !important;',
            )
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $form->setAction($this->getSaveUrl());
        $form->setId('edit_form');
        $form->setMethod('post');
        $form->setEnctype('multipart/form-data');
        $this->setForm($form);

        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap($page_type->getHtmlId(), $page_type->getName())
                ->addFieldMap($page_image->getHtmlId(), $page_image->getName())
                ->addFieldMap($page_text->getHtmlId(), $page_text->getName())
                ->addFieldDependence(
                    $page_text->getName(),
                    $page_type->getName(),
                    'Text'
                )
                ->addFieldDependence(
                    $page_image->getName(),
                    $page_type->getName(),
                    'Image'
                )
        );
        return parent::_prepareForm();
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }

    protected function _getMagazinesValuesForForm()
    {
        return Mage::getResourceModel('flippingbook/magazine_collection')->toOptionArray();
    }
}

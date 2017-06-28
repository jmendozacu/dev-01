<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Magazine_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('flippingbook_magazine');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('magazine_details_');

        $fieldset_details = $form->addFieldset('base_fieldset',
            array(
                'legend' => $this->__('Book Details'),
                'class'  => 'fieldset-wide'
            )
        );

        if ($model->getId()) {
            $fieldset_details->addField('magazine_id',
                'hidden',
                array(
                    'name' => 'magazine_id'
                )
            );
        }

        $fieldset_details->addField('magazine_title',
            'text',
            array(
                'name'     => 'magazine_title',
                'label'    => $this->__('Book Title'),
                'title'    => $this->__('Book Title'),
                'required' => true,
            )
        );

        $fieldset_details->addField('is_active',
            'select',
            array(
                'name'     => 'is_active',
                'label'    => $this->__('Book Status'),
                'title'    => $this->__('Book Status'),
                'required' => true,
                'options'  => array(
                    1 => Mage::helper('cms')->__('Enabled'),
                    0 => Mage::helper('cms')->__('Disabled')
                )
            )
        );

        $fieldset_details->addField('magazine_sort_order',
            'text',
            array(
                'name'  => 'magazine_sort_order',
                'label' => $this->__('Book Position'),
                'title' => $this->__('Book Position'),
                'class' => 'validate-digits',
                'style' => 'width: 30px !important;',
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset_details->addField('store_id',
                'multiselect',
                array(
                    'name'     => 'stores[]',
                    'label'    => Mage::helper('cms')->__('Store view'),
                    'title'    => Mage::helper('cms')->__('Store view'),
                    'required' => true,
                    'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
                )
            );
        } else {
            $fieldset_details->addField('store_id',
                'hidden',
                array(
                    'name'  => 'stores[]',
                    'value' => Mage::app()->getStore(true)->getId()
                )
            );

            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset_details->addField('magazine_category_id',
            'select',
            array(
                'name'     => 'magazine_category_id',
                'label'    => $this->__('Book Category'),
                'title'    => $this->__('Book Category'),
                'required' => true,
                'values'   => $this->_getCategoriesValuesForForm()
            )
        );

        $fieldset_details->addField('magazine_template_id',
            'select',
            array(
                'name'     => 'magazine_template_id',
                'label'    => $this->__('Book Template'),
                'title'    => $this->__('Book Template'),
                'required' => true,
                'values'   => $this->_getTemplatesValuesForForm()
            )
        );

        $fieldset_details->addField('magazine_resolution_id',
            'select',
            array(
                'name'     => 'magazine_resolution_id',
                'label'    => $this->__('Book Resolution'),
                'title'    => $this->__('Book Resolution'),
                'required' => true,
                'values'   => $this->_getResolutionsValuesForForm()
            )
        );

        $fieldset_details->addField('magazine_imgsub',
            'checkbox',
            array(
                'name'    => 'magazine_imgsub',
                'label'   => $this->__("Use subfolder for the book's files"),
                'title'   => $this->__("Use subfolder for the book's files"),
                'checked' => $model->getData('magazine_imgsub'),
            )
        );

        $fieldset_details->addField('magazine_imgsubfolder',
            'text',
            array(
                'name'     => 'magazine_imgsubfolder',
                'label'    => $this->__("Subfolder name"),
                'title'    => $this->__("Subfolder name"),
                'note'     => $this->__("Please, use only latin letters and numbers"),
                'disabled' => !($model->getData('magazine_imgsub')),
            )
        );

        $form->getElement('magazine_imgsub')
            ->setOnclick(
                "javascript:document.getElementById('" . $form->getHtmlIdPrefix() . $form->getElement('magazine_imgsubfolder')->getId() . "').disabled = !document.getElementById('" . $form->getHtmlIdPrefix() . $form->getElement('magazine_imgsubfolder')->getId() . "').disabled"
            );


        $fieldset_details->addField('magazine_thumbnail',
            'image',
            array(
                'name'     => 'magazine_thumbnail',
                'label'    => $this->__('Book thumbnail'),
                'note'     => $this->__('Select gif, jpg or png files')
            )
        );

        if ($model->getData('magazine_thumbnail')) {
            $model->setData('magazine_thumbnail', Mage::helper('flippingbook')->getPathUrl('thumbnail') . '/' . $model->getData('magazine_thumbnail'));
        }

        $fieldset_details->addField('magazine_view_mode',
            'select',
            array(
                'name'     => 'magazine_view_mode',
                'label'    => $this->__('Show book mode'),
                'title'    => $this->__('Show book mode'),
                'required' => true,
                'options'  => array(
                    1 => $this->__('Popup window'),
                    0 => $this->__('Direct link')
                )
            )
        );

        $fieldset_details->addField('url_direct_link',
          'text',
          array(
            'name'     => 'url_direct_link',
            'label'    => $this->__("Url for direct link"),
            'title'    => $this->__("Url for direct link"),
          )
        );

      $fieldset_details->addField('description',
        'textarea',
        array(
          'name'     => 'description',
          'label'    => $this->__('Description'),
          'title'    => $this->__('Description'),
          'required' => false
        )
      );

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }


    protected function _getCategoriesValuesForForm()
    {
        return Mage::getResourceModel('flippingbook/category_collection')->toOptionArray();
    }


    protected function _getTemplatesValuesForForm()
    {
        return Mage::getResourceModel('flippingbook/template_collection')->toOptionArray();
    }


    protected function _getResolutionsValuesForForm()
    {
        return Mage::getResourceModel('flippingbook/resolution_collection')->toOptionArray();
    }
}

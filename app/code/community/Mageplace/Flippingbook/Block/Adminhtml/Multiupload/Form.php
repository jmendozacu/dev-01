<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Multiupload_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();

		/* Upload Parameters Fieldset */
		$fieldset_parameters = $form->addFieldset('parameters_fieldset',
			array(
				'legend' => $this->__('Upload Parameters')
			)
		);

		$fieldset_parameters->addField('magazine_id',
			'select',
			array(
				'name'		=> 'magazine_id',
				'label'		=> $this->__('Select a book for the pages'),
				'title'		=> $this->__('Select a book for the pages'),
				'required'	=> true,
				'values'	=> $this->_getMagazinesValuesForForm()
			)
		);

		$fieldset_parameters->addField('page_title',
			'text',
			array(
				'name'		=> 'page_title',
				'label'		=> $this->__('Input general title for pages'),
				'title'		=> $this->__('Input general title for pages'),
				'required'	=> true,
			)
		);

		$source_type = $fieldset_parameters->addField('source_type',
			'select',
			array(
				'name'		=> 'source_type',
				'label'		=> $this->__('Select multiupload type'),
				'title'		=> $this->__('Select multiupload type'),
				'required'	=> true,
				'options'	=> array (
						'file'	=> $this->__('Upload Package File'),
						'dir'	=> $this->__('Install from Directory'),
				)
			)
		);



        $fieldset_upload = $fieldset_parameters->addField('upload_package',
			'file',
			array(
				'name'		=> 'upload_package',
				'label'		=> $this->__('Package File'),
				'note'		=> $this->__('Select zip files')
			)
		);


        $fieldset_install = $fieldset_parameters->addField('input_dir',
			'text',
			array(
				'name'		=> 'input_dir',
				'label'		=> $this->__('Input Directory'),
				'title'		=> $this->__('Input Directory'),
				'value'		=> 'media/',
				'disabled'	=> true
			)
		);

        $delete_files = $fieldset_parameters->addField('delete_files',
			'checkbox',
			array(
				'name'		=> 'delete_files',
				'label'		=> $this->__("Delete source files from directory after upload"),
				'title'		=> $this->__("Delete source files from directory after upload"),
				'value'		=> 1,
				'disabled'	=> true
			)
		);

		$form->setUseContainer(true);
		$form->setId('edit_form');
		$form->setMethod('post');
		$form->setEnctype('multipart/form-data');
		$form->setAction($this->getSaveUrl());
		$this->setForm($form);

        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap($source_type->getHtmlId(), $source_type->getName())
                ->addFieldMap($fieldset_upload->getHtmlId(), $fieldset_upload->getName())
                ->addFieldMap($delete_files->getHtmlId(), $delete_files->getName())
                ->addFieldMap($fieldset_install->getHtmlId(), $fieldset_install->getName())
                ->addFieldDependence(
                    $fieldset_install->getName(),
                    $source_type->getName(),
                    'dir'
                )
                ->addFieldDependence(
                    $fieldset_upload->getName(),
                    $source_type->getName(),
                    'file'
                )
                ->addFieldDependence(
                    $delete_files->getName(),
                    $source_type->getName(),
                    'dir'
                )
        );
	}

	
	protected function _getMagazinesValuesForForm()
	{
		return Mage::getResourceModel('flippingbook/magazine_collection')->toOptionArray();
	}

	public function getSaveUrl()
	{
		return $this->getUrl('*/*/save');
	}
}

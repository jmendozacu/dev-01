<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Magazine_Edit_Tab_Pdf extends Mage_Adminhtml_Block_Widget_Form
{

	protected function _prepareForm()
	{
		$model = Mage::registry('flippingbook_magazine');

		$form = new Varien_Data_Form();

		$fieldset_pdf = $form->addFieldset('pdf_fieldset',
			array(
				'legend'	=> $this->__('PDF options'),
				'class'		=> 'fieldset-wide'
			)
		);

		$fieldset_pdf->addField('magazine_enable_pdf',
			'select',
			array(
				'name'		=> 'magazine_enable_pdf',
				'label'		=> $this->__('Enable PDF'),
				'title'		=> $this->__('Enable PDF'),
				'options'	=> array (
					1 => Mage::helper('cms')->__('Enabled'),
					0 => Mage::helper('cms')->__('Disabled')
				)
			)
		);

		$fieldset_pdf->addType('fileext', Mage::getConfig()->getBlockClassName('flippingbook/form_element_fileext'));

		$fieldset_pdf->addField('magazine_background_pdf',
			'fileext',
			array(
				'name'		=> 'magazine_background_pdf',
				'label'		=> $this->__('PDF file'),
				'title'		=> basename($model->getData('magazine_background_pdf')),
			)
		);

		if($model->getData('magazine_background_pdf')) {
			$model->setData('magazine_background_pdf', Mage::helper('flippingbook')->getPathUrl('pdf').'/'.$model->getData('magazine_background_pdf'));
		}

		$form->setHtmlIdPrefix('magazine_pdf_');
		$form->setValues($model->getData());

		$this->setForm($form);

		return parent::_prepareForm();
	}
}

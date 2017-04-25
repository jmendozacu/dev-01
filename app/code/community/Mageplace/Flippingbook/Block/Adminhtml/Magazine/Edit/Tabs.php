<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Magazine_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();

		$this->setId('magazine_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle($this->__('Book information'));
	}

	protected function _prepareLayout()
	{
		$return = parent::_prepareLayout();

		$this->addTab(
			'details_section',
			array(
				'label'		=> $this->__('Book Details'),
				'title'		=> $this->__('Book Details'),
				'content'	=> $this->getLayout()->createBlock('flippingbook/adminhtml_magazine_edit_tab_details')->toHtml(),
				'active'	=> true,
			)
		);

		$this->addTab(
			'pdf_section',
			array(
				'label'		=> $this->__('PDF options'),
				'title'		=> $this->__('PDF options'),
				'content'	=> $this->getLayout()->createBlock('flippingbook/adminhtml_magazine_edit_tab_pdf')->toHtml(),
			)
		);



		return $return;
	}
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Multiupload extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		$this->_blockGroup = 'flippingbook';
		$this->_controller = 'adminhtml';
		$this->_mode = 'multiupload';

		parent::__construct();

		$this->_removeButton('reset');
		$this->_removeButton('back');
		$this->_updateButton('save', 'label', $this->__('Create pages'));
		$this->_updateButton('save', 'id', 'save_button');
	}

	public function getHeaderText()
	{
		return $this->__('Multiupload');
	}

	public function getHeaderCssClass()
	{
		return 'icon-head head-backups-control';
	}
}
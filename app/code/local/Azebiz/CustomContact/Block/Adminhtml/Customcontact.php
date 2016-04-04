<?php


class Azebiz_CustomContact_Block_Adminhtml_Customcontact extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_customcontact";
	$this->_blockGroup = "customcontact";
	$this->_headerText = Mage::helper("customcontact")->__("Customcontact Manager");
	$this->_addButtonLabel = Mage::helper("customcontact")->__("Add New Item");
	parent::__construct();

	}

}
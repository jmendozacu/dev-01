<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Customaddress_Model_City extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('customaddress/city');
	}
	
	public function toOptionArray() {
			$field_name = Mage::helper('customaddress')->getTextColumnName(); 
			$options = $this->_toOptionArray('region_id', $field_name, array('title' => $field_name));
			if (count($options) > 0) {
					array_unshift($options, array(
							'title '=> null,
							'value' => "",
							'label' => Mage::helper('directory')->__('-- Please select --')
					));
			}
			return $options;
	}
}
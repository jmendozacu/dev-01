<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Customaddress_Model_Subdistrict extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('customaddress/subdistrict');
	}
}
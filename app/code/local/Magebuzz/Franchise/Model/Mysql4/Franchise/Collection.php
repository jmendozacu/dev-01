<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Franchise_Model_Mysql4_Franchise_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
  public function _construct() {
    parent::_construct();
    $this->_init('franchise/franchise');
  }
}
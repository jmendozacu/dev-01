<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Franchise_Model_Mysql4_Franchise extends Mage_Core_Model_Mysql4_Abstract {
  public function _construct() {
    $this->_init('franchise/franchise', 'franchise_id');
  }
}

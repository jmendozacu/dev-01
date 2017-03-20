<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Career_Model_Mysql4_Application extends Mage_Core_Model_Mysql4_Abstract {
  public function _construct() {
    $this->_init('career/application', 'application_id');
  }
}

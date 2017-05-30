<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Career_Model_Mysql4_Worktype_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
  public function _construct() {
    parent::_construct();
    $this->_init('career/worktype');
  }
}
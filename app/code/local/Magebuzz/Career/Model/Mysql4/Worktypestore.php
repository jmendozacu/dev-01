<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Career_Model_Mysql4_Worktypestore extends Mage_Core_Model_Mysql4_Abstract {
  public function _construct() {
    // Note that the bannerads_id refers to the key field in your database table.
    $this->_init('career/worktypestore', 'worktype_id');
  }
}
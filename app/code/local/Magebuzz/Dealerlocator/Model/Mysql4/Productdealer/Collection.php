<?php
class Magebuzz_Dealerlocator_Model_Mysql4_Productdealer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
  public function _construct() {
    parent::_construct();
    $this->_init('dealerlocator/productdealer');
  }
}
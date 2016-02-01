<?php
class Magebuzz_Dealerlocator_Model_Mysql4_Productdealer extends Mage_Core_Model_Mysql4_Abstract {
  public function _construct() {
    $this->_init('dealerlocator/productdealer', 'productdealer_id');
  }
}
<?php
class Magebuzz_Dealerlocator_Model_Mysql4_Productdealertemp extends Mage_Core_Model_Mysql4_Abstract {
    public function _construct() {
        $this->_init('dealerlocator/productdealertemp', 'dealer_id');
    }
}
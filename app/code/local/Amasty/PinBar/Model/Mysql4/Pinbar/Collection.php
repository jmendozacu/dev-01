<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_PinBar
 */


class Amasty_PinBar_Model_Mysql4_Pinbar_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected function _construct() {
        $this->_init('ampinbar/pinbar');
    }

}
?>
<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_PinBar
 */


class Amasty_PinBar_Model_Mysql4_Pinbar extends Mage_Core_Model_Mysql4_Abstract {

    protected function _construct() {
        $this->_init('ampinbar/pinbar', 'pin_id');
    }
}
?>
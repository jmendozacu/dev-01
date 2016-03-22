<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_PinBar
 */


class Amasty_PinBar_Model_Session extends Mage_Core_Model_Abstract {

    public function __construct() {
        $this->_init('ampinbar/session');
    }
}

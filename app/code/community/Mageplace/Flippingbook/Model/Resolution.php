<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Model_Resolution extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('flippingbook/resolution');
    }


    public function getName()
    {
        return $this->getResolutionName();
    }


}
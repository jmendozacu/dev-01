<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprules
 */
class Amasty_Shiprules_Block_Cart_Shipping extends Amasty_Shiprules_Block_Cart_Shipping_Pure
{
    public function getShippingPrice($price, $flag)
    {
        return Mage::helper('amshiprules')->getShippingPrice($this, $price, $flag);
    }
}
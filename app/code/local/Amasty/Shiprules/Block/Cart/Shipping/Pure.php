<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprules
 */
if (Mage::helper('core')->isModuleEnabled('Amasty_Methods')) {
    class Amasty_Shiprules_Block_Cart_Shipping_Pure extends Amasty_Methods_Block_Rewrite_Checkout_Cart_Shipping {}
} else {
    class Amasty_Shiprules_Block_Cart_Shipping_Pure extends Mage_Checkout_Block_Cart_Shipping {}
}
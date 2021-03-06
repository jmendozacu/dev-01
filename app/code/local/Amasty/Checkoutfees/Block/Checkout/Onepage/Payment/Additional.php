<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Checkoutfees
 */
class Amasty_Checkoutfees_Block_Checkout_Onepage_Payment_Additional extends Mage_Core_Block_Template
{

    public function canShowBlock()
    {
        if (!Mage::getStoreConfig('amcheckoutfees/general/enabled')) {
            return false;
        }

        return true;
    }

    public function getFees($type = 'payment')
    {
        if (!Mage::getStoreConfig('amcheckoutfees/general/enabled')) {
            return false;
        }

        $fees = Mage::helper('amcheckoutfees')->getFees($type);

        return $fees;
    }

}

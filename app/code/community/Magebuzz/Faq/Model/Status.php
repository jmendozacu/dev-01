<?php
/**
 * @copyright   Copyright (c) 2013 AZeBiz Co. LTD
 */
class Magebuzz_Faq_Model_Status extends Varien_Object {
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 0;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('faq')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('faq')->__('Disabled')
        );
    }
}
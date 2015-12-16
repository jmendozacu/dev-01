<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Birth
 */  
class Amasty_Birth_Model_Customer_Customer extends Mage_Customer_Model_Customer
{
    protected $_coupon = null;
    
    public function getCoupon()
    {
	if (is_null($this->_coupon)){
            $this->_coupon = Mage::helper('ambirth')->generateCoupon('reg', $this->getName());
        }
        return $this->_coupon;
    }
}
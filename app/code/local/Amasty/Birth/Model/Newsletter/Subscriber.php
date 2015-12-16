<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Birth
 */  
class Amasty_Birth_Model_Newsletter_Subscriber extends Mage_Newsletter_Model_Subscriber
{
    public $_coupon = null;
    
    public function getCoupon()
    {
        if(Mage::getStoreConfig('ambirth/newsletter/enabled')){
            return $this->_coupon;
        }
        return '';
    } 

    public function loadByEmail($subscriberEmail)
    {
        $data = $this->getResource()->loadByEmail($subscriberEmail);
        if(empty ($data)){
            if(Mage::getStoreConfig('ambirth/newsletter/enabled')){
                    $this->_coupon = Mage::helper('ambirth')->generateCoupon('newsletter', 0, $subscriberEmail);
                }

        }
        $this->addData($data);
        return $this;
    }
}
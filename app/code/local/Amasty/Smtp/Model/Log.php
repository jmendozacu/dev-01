<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
class Amasty_Smtp_Model_Log extends Mage_Core_Model_Abstract
{
    const STATUS_SENT    = 'sent';
    const STATUS_FAILED  = 'failed';
    const STATUS_PENDING = 'pending';

    protected function _construct()
    {
        $this->_init('amsmtp/log');
    }

    public function truncate()
    {
        $this->getResource()->truncate();
    }

    public function autoClear()
    {
        $days = Mage::getStoreConfig('amsmtp/clear/email');
        if ($days)
        {
            Mage::helper('amsmtp')->debug('Starting to auto clear sent log (after ' . $days . ' days)');
            $this->getResource()->clear($days);
        }
    }

    public function getAvailableStatuses()
    {
        $statuses = new Varien_Object(array(
            self::STATUS_SENT => Mage::helper('amsmtp')->__('Successfully Sent'),
            self::STATUS_PENDING => Mage::helper('amsmtp')->__('Pending'),
            self::STATUS_FAILED => Mage::helper('amsmtp')->__('Failed'),
        ));
        return $statuses->getData();
    }
}
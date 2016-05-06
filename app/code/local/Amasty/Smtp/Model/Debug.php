<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
class Amasty_Smtp_Model_Debug extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('amsmtp/debug');
    }

    public function truncate()
    {
        $this->getResource()->truncate();
    }

    public function autoClear()
    {
        $days = Mage::getStoreConfig('amsmtp/clear/debug');
        if ($days)
        {
            Mage::helper('amsmtp')->debug('Starting to auto clear debug log (after ' . $days . ' days)');
            $this->getResource()->clear($days);
        }
    }
}
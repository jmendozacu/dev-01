<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_PinBar
 */


class Amasty_PinBar_Block_Adminhtml_Bar extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('amasty/ampinbar/bar.phtml');
    }

    public function visibleBar() {
        if ($this->getPinBarCollection()->getSize()) {
            return true;
        } else {
            return false;
        }
    }

    public function getPinBarCollection() {
        $pinCollection = Mage::getModel('ampinbar/pinbar')->getCollection();
        if (!Mage::helper('ampinbar')->sharedPins()) {
            $pinCollection->addFieldToFilter('user_id', Mage::getSingleton('admin/session')->getUser()->getUserId());
        }
        return $pinCollection;
    }

}
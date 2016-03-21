<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_PinBar
 */


class Amasty_PinBar_Block_Adminhtml_Params extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('amasty/ampinbar/params.phtml');
    }

    public function getParams() {
        return Mage::getSingleton('ampinbar/params')->getParams();
    }

}
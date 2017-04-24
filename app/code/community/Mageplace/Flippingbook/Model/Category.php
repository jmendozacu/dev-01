<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Model_Category extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('flippingbook/category');
    }

    public function getName()
    {
        return $this->getCategoryName();
    }

}
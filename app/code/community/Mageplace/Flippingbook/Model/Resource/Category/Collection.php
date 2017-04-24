<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */

class Mageplace_Flippingbook_Model_Resource_Category_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('flippingbook/category');
    }

    public function toOptionHash()
    {
        return $this->_toOptionHash('category_id', 'category_name');
    }


    public function toOptionArray()
    {
        return $this->_toOptionArray('category_id', 'category_name');
    }

}
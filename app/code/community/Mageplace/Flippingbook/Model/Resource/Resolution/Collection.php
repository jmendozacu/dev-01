<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Model_Resource_Resolution_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('flippingbook/resolution');
    }

    public function toOptionHash()
    {
        return $this->_toOptionHash('resolution_id', 'resolution_name');
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('resolution_id', 'resolution_name');
    }

}
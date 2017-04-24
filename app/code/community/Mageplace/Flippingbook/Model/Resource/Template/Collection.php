<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Model_Resource_Template_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('flippingbook/template');
    }

    public function toOptionHash()
    {
        return $this->_toOptionHash('template_id', 'template_name');
    }


    public function toOptionArray()
    {
        return $this->_toOptionArray('template_id', 'template_name');
    }

}
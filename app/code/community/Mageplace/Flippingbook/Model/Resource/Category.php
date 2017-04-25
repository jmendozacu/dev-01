<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */

class Mageplace_Flippingbook_Model_Resource_Category extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('flippingbook/category', 'category_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if(!$object->getId()) {
            $object->setCreationDate(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdateDate(Mage::getSingleton('core/date')->gmtDate());

        return $this;
    }


    public function getCategoryNameById($id)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from(array('main_table' => $this->getMainTable()), 'category_name')
            ->where('main_table.category_id = ?', $id);

        return $this->_getReadAdapter()->fetchOne($select);
    }

}
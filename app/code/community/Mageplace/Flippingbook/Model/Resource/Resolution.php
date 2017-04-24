<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Model_Resource_Resolution extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('flippingbook/resolution', 'resolution_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if(!$object->getId()) {
            $object->setCreationDate(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdateDate(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }


    public function getResolutionNameById($id)
    {
        $select = $this->_getReadAdapter()->select();
        /* @var $select Zend_Db_Select */
        $select->from(array('main_table' => $this->getMainTable()), 'resolution_name')
            ->where('main_table.resolution_id = ?', $id);

        return $this->_getReadAdapter()->fetchOne($select);
    }

}
<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Model_Resource_Template extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('flippingbook/template', 'template_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if(!$object->getId()) {
            $object->setCreationDate(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdateDate(Mage::getSingleton('core/date')->gmtDate());

        return $this;
    }


    public function getTemplateNameById($id)
    {
        $select = $this->_getReadAdapter()->select();
        /* @var $select Zend_Db_Select */
        $select->from(array('main_table' => $this->getMainTable()), 'template_name')
            ->where('main_table.template_id = ?', $id);

        return $this->_getReadAdapter()->fetchOne($select);
    }

}
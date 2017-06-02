<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Career_Model_Mysql4_Worktype extends Mage_Core_Model_Mysql4_Abstract {
  public function _construct() {
    $this->_init('career/worktype', 'worktype_id');
  }
  protected function _afterLoad(Mage_Core_Model_Abstract $object) {
    if ($object->getId()) {
      $stores = $this->lookupStoreIds($object->getId());
      $worktype = $this->lookupWorktypeId($object->getId());
      $object->setData('store_id', $stores);
      $object->setData('worktype_id', $worktype);
    }
    return parent::_afterLoad($object);
  }
  protected function _afterSave(Mage_Core_Model_Abstract $object) {
    $oldStores = $this->lookupStoreIds($object->getId());
    $newStores = (array)$object->getStores();
    $oldworktype = $this->lookupWorktypeId($object->getId());
    $newworktype = (array)$object->getWorktypeId();

    if (empty($newworktype)) {
      $newworktype = (array)$object->getWorktypeId();
    }

    if (empty($newStores)) {
      $newStores = (array)$object->getStoreId();
    }
    $this->saveStore($newStores, $oldStores, $object->getId());
    $this->saveWorktypeId($newworktype, $oldworktype, $object->getId());

    return parent::_afterSave($object);
  }
  public function saveStore($newStores, $oldStores, $worktypeId) {
    $table = $this->getTable('career/worktypestore');
    $insert = array_diff($newStores, $oldStores);
    $delete = array_diff($oldStores, $newStores);
    if ($delete) {
      $where = array('worktype_id = ?' => (int)$worktypeId, 'store_id IN (?)' => $delete);
      $this->_getWriteAdapter()->delete($table, $where);
    }
    if ($insert) {
      $data = array();
      foreach ($insert as $storeId) {
        $data[] = array('worktype_id' => (int)$worktypeId, 'store_id' => (int)$storeId);
      }
      $this->_getWriteAdapter()->insertMultiple($table, $data);
    }
  }

  public function saveWorktypeId($newworktype, $oldworktype, $worktypeId) {
    $table = $this->getTable('career/worktype');
    $insert = array_diff($newworktype, $oldworktype);
    $delete = array_diff($oldworktype, $newworktype);
    if ($delete) {
      $where = array('worktype_id = ?' => (int)$worktypeId);
      $this->_getWriteAdapter()->delete($table, $where);
    }
    if ($insert) {
      $data = array();
      foreach ($insert as $bannerId) {
        $data[] = array('worktype_id' => (int)$worktypeId);
      }
      $this->_getWriteAdapter()->insertMultiple($table, $data);
    }
  }

  public function lookupStoreIds($worktype_id) {
    $adapter = $this->_getReadAdapter();
    $select = $adapter->select()->from($this->getTable('career/worktypestore'), 'store_id')->where('worktype_id = ?', (int)$worktype_id);
    return $adapter->fetchCol($select);
  }

  public function lookupWorktypeId($worktype_id) {
    $ids = array();
    $worktypes = Mage::getModel('career/worktype')->getCollection()->addFieldToFilter('worktype_id', (int)$worktype_id);
    foreach ($worktypes as $worktype) {
      $ids[] = $worktype->getWorktypeId();
    }
    return $ids;
  }
}
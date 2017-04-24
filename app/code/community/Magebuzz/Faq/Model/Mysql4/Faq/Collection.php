<?php

/**
 * @copyright   Copyright (c) 2013 AZeBiz Co. LTD
 */
class Magebuzz_Faq_Model_Mysql4_Faq_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
  public function _construct()
  {
    parent::_construct();
    $this->_init('faq/faq');
    $this->_map['fields']['faq_id'] = 'main_table.faq_id';

    $this->_map['fields']['store'] = 'store_table.store_id';
  }


  public function addStoreFilter($store)
  {
    if (!$this->getFlag('store_filter_added')) {
      if ($store instanceof Mage_Core_Model_Store) {
        $store = array($store->getId());
      }
      if (!is_array($store)) {
        $store = array($store);
      }

      $this->addFilter('store', array('in' => $store), 'public');
    }
    return $this;
  }
  protected function _renderFiltersBefore()
  {
    if ($this->getFilter('store')) {
      $this->getSelect()->join(
        array('store_table' => $this->getTable('faq/faq_store')),
        'main_table.faq_id = store_table.faq_id',
        array()
      )->group('main_table.faq_id');

      /*
       * Allow analytic functions usage because of one field grouping
       */
      $this->_useAnalyticFunction = true;
    }
    return parent::_renderFiltersBefore();
  }
  public function getSelectCountSql()
  {
    $countSelect = parent::getSelectCountSql();

    $countSelect->reset(Zend_Db_Select::GROUP);

    return $countSelect;
  }
}

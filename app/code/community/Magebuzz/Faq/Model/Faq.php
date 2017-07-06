<?php
/**
 * @copyright   Copyright (c) 2013 AZeBiz Co. LTD
 */
class Magebuzz_Faq_Model_Faq extends Mage_Core_Model_Abstract
{
	public function _construct() {
		parent::_construct();
		$this->_init('faq/faq');
	}
	
	public function getQuestionId($object) {
		$condition = $this->getResource()->_getWriteAdapter()->quoteInto('faq_id = ?', $object->getData('category_id'));
		$this->_getWriteAdapter()->load($this->getTable('faq/category_item'), $condition);
		return $this;
	} 
	
	public function getFaqsByIds($faqIds){
		$faqCollection = $this->getCollection()
						->addFieldToFilter('is_active', 1)
						->addFieldToFilter('faq_id', array('in' => $faqIds));
		return $faqCollection;				
	}
	public function getAllFaqs($categories)
	{
		$storeIds = array(Mage::app()->getStore()->getId(), Mage_Core_Model_App::ADMIN_STORE_ID);
		$collection = Mage::getModel('faq/faq')->getCollection();
		$collection->getSelect()
			->join(array('faq_category' => Mage::getModel('core/resource')->getTableName('faq_category_item')), 'main_table.faq_id=faq_category.faq_id')
			->join(array('fstore' => Mage::getModel('core/resource')->getTableName('faq_store')), 'main_table.faq_id=fstore.faq_id')
			->where('faq_category.category_id IN (?)', $categories)
			->where('fstore.store_id IN (?)', $storeIds)
			->group('fstore.faq_id');
		;
		return $collection;
	}
}
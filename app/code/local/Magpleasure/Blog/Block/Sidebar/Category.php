<?php
    /**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Magpleasure_Blog
 */

class Magpleasure_Blog_Block_Sidebar_Category extends Magpleasure_Blog_Block_Sidebar_Abstract
{
    protected $_collection;
    protected $_collection_subcate;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("mpblog/sidebar/categories.phtml");
        $this->_route = 'use_categories';

        $cacheTags = $this->getCacheTags();
        $cacheTags[] = Magpleasure_Blog_Model_Category::CACHE_TAG;
        $this->setCacheTags($cacheTags);
    }

    public function getBlockHeader()
    {
        return $this->__('Categories');
    }

    protected function _getCacheParams()
    {
        $params = parent::_getCacheParams();
        $params[] = 'category';

        if ($this->_isRequestMatchParams('mpblog', 'index', 'category')){
            $params[] = 1;
            $params[] = $this->getRequest()->getParam('id');
        } else {
            $params[] = 0;
        }

        return $params;
    }

    public function getCollection()
    {
        if (!$this->_collection){
            /** @var Magpleasure_Blog_Model_Mysql4_Category_Collection $collection  */
            $collection = Mage::getModel('mpblog/category')->getCollection();
            $collection->addFieldToFilter('status', Magpleasure_Blog_Model_Category::STATUS_ENABLED);

            if (!Mage::app()->isSingleStoreMode()){
                $collection->addStoreFilter(Mage::app()->getStore()->getId());
            }

            $collection->setSortOrder('asc');

            $this->_collection = $collection;
        }
        return $this->_collection;
    }
    public function getCollectionSubcate($subcate_id)
    {
        if ($subcate_id){
            /** @var Magpleasure_Blog_Model_Mysql4_Category_Collection $collection  */
            $collection = Mage::getModel('mpblog/category')->load($subcate_id);

            $this->_collection_subcate = $collection;
        }
        return $this->_collection_subcate;
    }

  public function getCollectionByLanding($typelanding){
    $collection = Mage::getModel('mpblog/category')->getCollection();
    $collection->addFieldToFilter('status', Magpleasure_Blog_Model_Category::STATUS_ENABLED);
    $collection->addFieldToFilter($typelanding, 1);

    if (!Mage::app()->isSingleStoreMode()){
      $collection->addStoreFilter(Mage::app()->getStore()->getId());
    }

    return $collection->setSortOrder('asc');
  }
}
<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Magpleasure_Blog
 */

class Magpleasure_Blog_Block_Content_Category_List extends Magpleasure_Blog_Block_Content_Category
{
    protected $_collection;

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
}

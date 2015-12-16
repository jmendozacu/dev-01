<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */

/**
 * @author MarginFrame
 */ 
class MarginFrame_Shopby_Model_Mysql4_Page extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('amshopby/page', 'page_id');
    }

    /**
     * @param int $categoryId
     * @return MarginFrame_Shopby_Model_Page|null
     */
    public function getCurrentMatchedPage($categoryId)
    {
        $result = null;

        /** @var MarginFrame_Shopby_Model_Mysql4_Page_Collection $collection */
        $collection = Mage::getModel('amshopby/page')->getCollection();
        $collection->addStoreFilter();
        if ($categoryId) {
            $collection->addCategoryFilter($categoryId);
        }

        foreach ($collection as $page){
            /** @var MarginFrame_Shopby_Model_Page $page */

            if ($page->matchCurrentFilters()) {
                $result = $page;
                break;
            }
        }

        return $result;
    }
}
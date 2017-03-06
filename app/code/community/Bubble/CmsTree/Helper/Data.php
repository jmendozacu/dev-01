<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @param null|int $id
     * @return Mage_Core_Model_Store
     */
    public function getStore($id = null)
    {
        return Mage::app()->getStore($id);
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return Mage::getSingleton('customer/session')->getCustomerGroupId();
    }

    /**
     * @return Bubble_CmsTree_Model_Cms_Page
     */
    public function getCurrentCmsPage()
    {
        $page = Mage::getSingleton('cms/page');
        if (count($page->getData()) > 0 && !$page->isRoot()) {
            return $page;
        }

        return false;
    }

    /**
     * @param null $storeId
     * @return Bubble_CmsTree_Model_Cms_Page
     */
    public function getCmsRootPage($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore()->getId();
        }

        return Mage::getModel('cms/page')->loadRootByStoreId($storeId);
    }

    /**
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getStoreCmsPages()
    {
        /** @var Varien_Data_Tree_Node_Collection $collection */
        $collection = $this->getCmsRootPage(0)->getChildren();
        foreach ($this->getCmsRootPage()->getChildren() as $page) {
            $collection->addItem($page);
        }

        return $collection;
    }

    /**
     * @param mixed $storeId
     * @return string
     */
    public function getUrlSuffix($storeId = null)
    {
        return Mage::getStoreConfig('bubble_cmstree/general/page_url_suffix', $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isRedirectNonSuffixedUrl($storeId = null)
    {
        return Mage::getStoreConfigFlag('bubble_cmstree/general/redirect_non_suffixed_url', $storeId);
    }
}

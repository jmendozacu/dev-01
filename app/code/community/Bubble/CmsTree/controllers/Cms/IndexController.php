<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
require_once 'Mage/Cms/controllers/IndexController.php';

class Bubble_CmsTree_Cms_IndexController extends Mage_Cms_IndexController
{
    /**
     * @param null $coreRoute
     */
    public function indexAction($coreRoute = null)
    {
        $store = Mage::app()->getStore();
        // Home page defined in config first
        $pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE);
        if (!$pageId || !is_numeric($pageId)) {
            // Root page of current store view
            $pageId = Mage::getResourceModel('cms/page')->getStoreRootId($store->getId());
        }
        if (!$pageId) {
            // Global home page
            $pageId = Mage::getResourceModel('cms/page')->getStoreRootId(0);
        }
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('defaultIndex');
        }
    }
}
<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Model_Cms_Page_Version extends Mage_Core_Model_Abstract
{
    /**
     * Initialization
     */
    protected function _construct()
    {
        $this->_init('bubble_cmstree/cms_page_version');
    }

    /**
     * @return Bubble_CmsTree_Model_Cms_Page
     */
    public function getPage()
    {
        return Mage::getModel('cms/page')->load($this->getPageId());
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $page = $this->getPage();
        $store = Mage::app()->getStore($page->getStoreId());
        if ($store->isAdmin()) {
            $store = Mage::app()->getDefaultStoreView();
        }
        $route = sprintf(
            'cms/page/view/page_id/%d/version/%d',
            $page->getId(),
            $this->getId()
        );

        return $store->getUrl($route, array('_query' => array('___store' => $store->getCode())));
    }
}
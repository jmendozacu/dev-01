<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Block_Adminhtml_Cms_Page_Permissions_Tree extends Mage_Adminhtml_Block_Template
{
    /**
     * @var Bubble_CmsTree_Model_Cms_Page
     */
    protected $_root;

    /**
     * Initialization
     */
    public function __construct()
    {
        parent::__construct();

        $selectedPages = Mage::getResourceModel('cms/page_permission')
            ->getPagesByStoreAndCustomerGroup($this->getStoreId(), $this->getCustomerGroupId());
        $this->setSelectedResources($selectedPages);
    }

    /**
     * @return Mage_Core_Model_Store
     * @throws Exception
     */
    public function getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store');

        return Mage::app()->getStore($storeId);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->getStore()->getId();
    }

    /**
     * @return Mage_Customer_Model_Group
     * @throws Exception
     */
    public function getCustomerGroup()
    {
        $groupId = (int) $this->getRequest()->getParam('group');

        return Mage::getModel('customer/group')->load($groupId);
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->getCustomerGroup()->getId();
    }

    /**
     * @return Bubble_CmsTree_Model_Cms_Page
     */
    public function getRoot()
    {
        if (null === $this->_root) {
            $storeId = $this->getStoreId();
            $this->_root = Mage::getModel('cms/page')->loadRootByStoreId($storeId);
            if (!$this->_root->getId()) {
                $this->_root = Bubble_CmsTree_Model_Cms_Page::createDefaultStoreRootPage($storeId);
            }
        }

        return $this->_root;
    }

    /**
     * @return int
     */
    public function getRootId()
    {
        return $this->getRoot()->getId();
    }

    /**
     * @return string
     */
    public function getResTreeJson()
    {
        $root = $this->getRoot();
        $rootArray = $this->_getNodeJson($root);
        $json = Mage::helper('core')->jsonEncode(array($rootArray));

        return $json;
    }

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortTree($a, $b)
    {
        return $a['sort_order'] < $b['sort_order'] ? -1 : ($a['sort_order'] > $b['sort_order'] ? 1 : 0);
    }

    /**
     * @param Bubble_CmsTree_Model_Cms_Page $page
     * @return array
     */
    protected function _getNodeJson(Bubble_CmsTree_Model_Cms_Page $page)
    {
        $item = array();
        $selres = $this->getSelectedResources();

        $item['text'] = $page->getTitle();
        $item['sort_order'] = $page->getPosition();
        $item['id'] = $page->getId();

        if (in_array($item['id'], $selres)) {
            $item['checked'] = true;
        }

        $children = $page->getChildren();

        if (empty($children)) {
            return $item;
        }

        if ($children) {
            $item['children'] = array();
            foreach ($children as $child) {
                $item['children'][] = $this->_getNodeJson($child);
            }
            if (!empty($item['children'])) {
                usort($item['children'], array($this, '_sortTree'));
            }
        }

        return $item;
    }
}
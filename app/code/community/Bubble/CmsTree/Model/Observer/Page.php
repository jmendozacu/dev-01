<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Model_Observer_Page
{
    /**
     * @var Bubble_CmsTree_Helper_Data
     */
    protected $_helper;

    /**
     * Initialization
     */
    public function __construct()
    {
        $this->_helper = Mage::helper('bubble_cmstree');
    }

    /**
     * Adds CMS pages to top menu
     *
     * @param Varien_Event_Observer $observer
     */
    public function beforeTopmenuHtml(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfigFlag('bubble_cmstree/general/include_in_menu')) {
            $block = $observer->getEvent()->getBlock();
            $block->addCacheTag(Mage_Cms_Model_Page::CACHE_TAG);
            $this->_addPagesToMenu(
                $this->_helper->getStoreCmsPages(), $observer->getMenu(), $block, true
            );
        }
    }

    /**
     * Recursively adds CMS pages to top menu
     *
     * @param Varien_Data_Tree_Node_Collection|array $pages
     * @param Varien_Data_Tree_Node $parentPageNode
     * @param Mage_Page_Block_Html_Topmenu $menuBlock
     * @param bool $addTags
     */
    protected function _addPagesToMenu($pages, $parentPageNode, $menuBlock, $addTags = false)
    {
        $pageModel = Mage::getModel('cms/page');
        foreach ($pages as $page) {
            /** @var Bubble_CmsTree_Model_Cms_Page $page */
            if (!$page->getIsActive() || !$page->getIncludeInMenu() || !$this->_isAllowed($page)) {
                continue;
            }

            $nodeId = 'page-node-' . $page->getId();

            $pageModel->setId($page->getId());
            if ($addTags) {
                $menuBlock->addModelTags($pageModel);
            }

            $tree = $parentPageNode->getTree();
            $pageData = array(
                'name'      => $page->getTitle(),
                'id'        => $nodeId,
                'url'       => $page->getUrl(),
                'is_active' => $this->_isActiveMenuPage($page)
            );
            $pageNode = new Varien_Data_Tree_Node($pageData, 'id', $tree, $parentPageNode);
            $parentPageNode->addChild($pageNode);

            $children = $page->getChildren();
            if (Mage::helper('cms/page')->isPermissionsEnabled($this->_helper->getStore())) {
                $children->addPermissionsFilter($this->_helper->getCustomerGroupId());
            }

            $this->_addPagesToMenu($children, $pageNode, $menuBlock, $addTags);
        }
    }

    /**
     * @param Varien_Object $page
     * @return bool
     */
    protected function _isActiveMenuPage(Varien_Object $page)
    {
        $current = $this->_helper->getCurrentCmsPage();
        if ($current) {
            return in_array($page->getId(), $current->getPathIds());
        }

        return false;
    }

    /**
     * @param Varien_Object $page
     * @return bool
     */
    protected function _isAllowed(Varien_Object $page)
    {
        return Mage::helper('cms/page')->isAllowed(
            $this->_helper->getStore()->getId(),
            $this->_helper->getCustomerGroupId(),
            $page->getId()
        );
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function savePageAfter(Varien_Event_Observer $observer)
    {
        /** @var Bubble_CmsTree_Model_Cms_Page $page */
        $page = $observer->getEvent()->getObject();
        if ($page && $page->getId() && $page->getManageVersions() && !$page->getVersionSaved()) {
            $version = Mage::getModel('bubble_cmstree/cms_page_version')
                ->setData($page->getData())
                ->unsCreationTime()
                ->setUsername($this->_getUsername())
                ->save();
            $page->setVersionSaved(true);
            $version->getResource()->deletePageDrafts($page);
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function loadPageAfter(Varien_Event_Observer $observer)
    {
        $versionId = Mage::app()->getRequest()->getParam('version');
        if ($versionId) {
            $version = Mage::getModel('bubble_cmstree/cms_page_version')->load($versionId);
            $page = $observer->getEvent()->getObject();
            /** @var Bubble_CmsTree_Model_Cms_Page $page */
            if ($version->getId() && $version->getPageId() == $page->getId()) {
                $page->addData($version->getData());
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function prepareSave(Varien_Event_Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        /** @var Bubble_CmsTree_Model_Cms_Page $page */
        $page = $observer->getEvent()->getPage();
        if ($request->has('store')) {
            $page->setStoreId($request->getParam('store'));
        }
        if ($request->has('parent')) {
            $page->setParentId($request->getParam('parent'));
        }
    }

    /**
     * @return string|null
     */
    protected function _getUsername()
    {
        $username = null;
        if (Mage::getSingleton('api/session')->isLoggedIn()) {
            $username = Mage::getSingleton('api/session')->getUser()->getUsername();
        } elseif (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
        }

        return $username;
    }
}
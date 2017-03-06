<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Block_Adminhtml_Cms_Page_Tree extends Mage_Adminhtml_Block_Template
{
    /**
     * @var bool
     */
    protected $_withChildrenCount;

    /**
     * Initialization
     */
    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->_withChildrenCount = true;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $addUrl = $this->getUrl("*/*/add", array(
            '_current'  => true,
            'id'        => null,
            '_query'    => false
        ));

        $data = array(
            'label'     => Mage::helper('cms')->__('Add Page'),
            'onclick'   => "addNew('" . $addUrl . "', false)",
            'class'     => 'add',
            'id'        => 'add_subpage_button',
            'style'     => $this->canAddSubPage() ? '' : 'display: none;'
        );
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData($data);
        $this->setChild('add_sub_button', $button);

        $switcher = $this->getLayout()->createBlock('adminhtml/store_switcher')
            ->setUseConfirm(Mage::getStoreConfigFlag('bubble_cmstree/general/confirm_switch'))
            ->setSwitchUrl($this->getUrl('*/*/switch', array('_current' => true, '_query' => false, 'store' => null)))
            ->setTemplate('bubble/cmstree/cms/page/store/switcher.phtml');
        $this->setChild('store_switcher', $switcher);

        return parent::_prepareLayout();
    }

    /**
     * @return Mage_Cms_Model_Resource_Page_Collection
     * @throws Exception
     */
    public function getPageCollection()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        /* @var $collection Mage_Cms_Model_Mysql4_Page_Collection */
        $collection = $this->getData('page_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('cms/page')->getCollection();
            $collection->addStoreFilter($storeId);
            $this->setData('page_collection', $collection);
        }

        return $collection;
    }

    /**
     * @return string
     */
    public function getAddRootButtonHtml()
    {
        return $this->getChildHtml('add_root_button');
    }

    /**
     * @return string
     */
    public function getAddSubButtonHtml()
    {
        return $this->getChildHtml('add_sub_button');
    }

    /**
     * @return string
     */
    public function getExpandButtonHtml()
    {
        return $this->getChildHtml('expand_button');
    }

    /**
     * @return string
     */
    public function getCollapseButtonHtml()
    {
        return $this->getChildHtml('collapse_button');
    }

    /**
     * @return string
     */
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    /**
     * @param null|bool $expanded
     * @return string
     */
    public function getLoadTreeUrl($expanded = null)
    {
        $params = array('_current' => true, 'id' => null, 'store' => null);
        if ((is_null($expanded) && Mage::getSingleton('admin/session')->getIsTreeWasExpanded()) || $expanded) {
            $params['expand_all'] = true;
        }

        return $this->getUrl('*/*/pagesJson', $params);
    }

    /**
     * @return string
     */
    public function getNodesUrl()
    {
        return $this->getUrl('*/cms_page/jsonTree');
    }

    /**
     * @return string
     */
    public function getSwitchTreeUrl()
    {
        return $this->getUrl('*/cms_page/tree', array(
            '_current'  => true,
            '_query'    => false,
            'store'     => null,
            'id'        => null,
            'parent'    => null,
        ));
    }

    /**
     * @return bool
     */
    public function getIsWasExpanded()
    {
        return Mage::getSingleton('admin/session')->getIsTreeWasExpanded();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/cms_page/move', array('store' => $this->getRequest()->getParam('store')));
    }

    /**
     * @param null|Bubble_CmsTree_Model_Cms_Page $parentNodePage
     * @return array
     */
    public function getTree($parentNodePage = null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parentNodePage));
        $tree = isset($rootArray['children']) ? $rootArray['children'] : array();

        return $tree;
    }

    /**
     * @param null|Bubble_CmsTree_Model_Cms_Page $parentNodePage
     * @return string
     */
    public function getTreeJson($parentNodePage = null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parentNodePage));
        $children = isset($rootArray['children']) ? $rootArray['children'] : array();
        $json = Mage::helper('core')->jsonEncode($children);

        return $json;
    }

    /**
     * @param string $path
     * @param string $javascriptVarName
     * @return string
     */
    public function getBreadcrumbsJavascript($path, $javascriptVarName)
    {
        if (empty($path)) {
            return '';
        }

        $pages = Mage::getResourceSingleton('cms/page_tree')
            ->setStoreId($this->getStore()->getId())->loadBreadcrumbsArray($path);
        if (empty($pages)) {
            return '';
        }

        foreach ($pages as $key => $page) {
            $pages[$key] = $this->_getNodeJson($page);
        }

        return
            '<script type="text/javascript">' .
            $javascriptVarName . ' = ' . Mage::helper('core')->jsonEncode($pages) . ';' .
            ($this->canAddSubPage() ? '$("add_subpage_button").show();' : '$("add_subpage_button").hide();') .
            '</script>';
    }

    /**
     * @param $node
     * @param int $level
     * @return array
     */
    protected function _getNodeJson($node, $level = 0)
    {
        // create a node from data array
        if (is_array($node)) {
            $node = new Varien_Data_Tree_Node($node, 'entity_id', new Varien_Data_Tree);
        }

        $item = array();
        $item['text'] = $this->buildNodeName($node);

        $rootForStores = in_array($node->getEntityId(), $this->getRootIds());

        $item['id'] = $node->getId();
        $item['store'] = (int) $this->getStore()->getId();
        $item['path'] = $node->getData('path');

        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        $allowMove = $this->_isPageMoveable($node);
        $item['allowDrop'] = $allowMove;
        // disallow drag if it's first level and page is root of a store
        $item['allowDrag'] = $allowMove && (($node->getLevel() == 1 && $rootForStores) ? false : true);

        if ((int)$node->getChildrenCount() > 0) {
            $item['children'] = array();
        }

        $isParent = $this->_isParentSelectedPage($node);

        if ($node->hasChildren()) {
            $item['children'] = array();
            if (!($this->getUseAjax() && $node->getLevel() > 1 && !$isParent)) {
                foreach ($node->getChildren() as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level + 1);
                }
            }
        }

        if ($isParent || $node->getLevel() < 2) {
            $item['expanded'] = true;
        }

        return $item;
    }

    /**
     * @param $node
     * @return string
     */
    public function buildNodeName($node)
    {
        $result = $this->escapeHtml($node->getTitle());
        if ($this->_withChildrenCount) {
             $result .= ' (' . $node->getChildrenCount() . ')';
        }

        return $result;
    }

    /**
     * @param $node
     * @return bool
     */
    protected function _isPageMoveable($node)
    {
        $options = new Varien_Object(array(
            'is_moveable' => true,
            'page' => $node
        ));

        Mage::dispatchEvent('adminhtml_page_tree_is_moveable', array('options' => $options));

        return $options->getIsMoveable();
    }

    /**
     * @param $node
     * @return bool
     */
    protected function _isParentSelectedPage($node)
    {
        if ($node && $this->getPage()) {
            $pathIds = $this->getPage()->getPathIds();
            if (in_array($node->getId(), $pathIds)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canAddRootPage()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function canAddSubPage()
    {
        $options = new Varien_Object(array('is_allow' => true));
        Mage::dispatchEvent(
            'adminhtml_page_tree_can_add_sub_page',
            array(
                'page'    => $this->getPage(),
                'options' => $options,
                'store'   => $this->getStore()->getId()
            )
        );

        return $options->getIsAllow();
    }

    /**
     * @return Bubble_CmsTree_Model_Cms_Page
     */
    public function getPage()
    {
        return Mage::registry('cms_page');
    }

    /**
     * @return int
     */
    public function getPageId()
    {
        if ($this->getPage()) {
            return $this->getPage()->getId();
        }

        return Mage::getResourceModel('cms/page')->getStoreRootId($this->getStoreId());
    }

    /**
     * @return string
     */
    public function getPageName()
    {
        return $this->getPage()->getName();
    }

    /**
     * @return string
     */
    public function getPagePath()
    {
        if ($this->getPage()) {
            return $this->getPage()->getPath();
        }

        return '';
    }

    /**
     * @return bool
     */
    public function hasStoreRootPage()
    {
        $root = $this->getRoot();
        if ($root && $root->getId()) {
            return true;
        }

        return false;
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
     * @param null|Bubble_CmsTree_Model_Cms_Page $parentNodePage
     * @param int $recursionLevel
     * @return Varien_Data_Tree_Node
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function getRoot($parentNodePage = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodePage) && $parentNodePage->getId()) {
            return $this->getNode($parentNodePage, $recursionLevel);
        }

        $root = Mage::registry('root');
        if (is_null($root)) {
            $storeId = (int) $this->getRequest()->getParam('store');
            $rootId = Mage::getResourceModel('cms/page')->getStoreRootId($storeId);
            if (!$rootId) {
                $newRoot = Bubble_CmsTree_Model_Cms_Page::createDefaultStoreRootPage($storeId);
                $rootId = $newRoot->getId();
            }

            $tree = Mage::getResourceSingleton('cms/page_tree')
                ->load(null, $recursionLevel);

            if ($this->getPage()) {
                $tree->loadEnsuredNodes($this->getPage(), $tree->getNodeById($rootId));
            }

            $tree->addCollectionData($this->getPageCollection());
            $root = $tree->getNodeById($rootId);

            if (!$root) {
                Mage::throwException('Could not retrieve root page of store ' . $storeId);
            }

            $root->setIsVisible(true);
            $root->setName($root->getTitle());
            if ($this->_withChildrenCount) {
                $root->setName($root->getName() . ' (' . $root->getChildrenCount() . ')');
            }

            Mage::register('root', $root);
        }

        return $root;
    }

    /**
     * @param array $ids
     * @return Varien_Data_Tree_Node
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function getRootByIds($ids)
    {
        $root = Mage::registry('root');
        if (null === $root) {
            $storeId = (int) $this->getRequest()->getParam('store');
            $pageTreeResource = Mage::getResourceSingleton('cms/page_tree');
            $ids = $pageTreeResource->getExistingPageIdsBySpecifiedIds($ids);
            $tree = $pageTreeResource->loadByIds($ids);
            $rootId = Mage::getResourceModel('cms/page')->getStoreRootId($storeId);
            if (!$rootId) {
                $newRoot = $this->_createStoreRootPage($storeId);
                $rootId = $newRoot->getId();
            }
            $root = $tree->getNodeById($rootId);

            if (!$root) {
                Mage::throwException('Could not retrieve root page of store ' . $storeId);
            }

            $tree->addCollectionData($this->getPageCollection());
            Mage::register('root', $root);
        }

        return $root;
    }

    /**
     * @param $parentNodePage
     * @param int $recursionLevel
     * @return Varien_Data_Tree_Node
     */
    public function getNode($parentNodePage, $recursionLevel = 2)
    {
        $tree = Mage::getResourceModel('cms/page_tree');
        $nodeId = $parentNodePage->getId();
        $node = $tree->loadNode($nodeId);
        $node->loadChildren($recursionLevel);
        $tree->addCollectionData($this->getPageCollection());

        return $node;
    }

    /**
     * @param array $args
     * @return string
     */
    public function getSaveUrl(array $args = array())
    {
        $params = array('_current' => true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getEditUrl()
    {
        return $this->getUrl('*/cms_page/edit', array(
            '_current'  => true,
            '_query'    => false,
            'store'     => $this->getRequest()->getParam('store'),
            'id'        => null,
            'parent'    => null
        ));
    }

    /**
     * @return array
     */
    public function getRootIds()
    {
        $ids = $this->getData('root_ids');
        if (is_null($ids)) {
            $ids = array();
            foreach (Mage::app()->getGroups() as $store) {
                $ids[] = $store->getRootPageId();
            }
            $this->setData('root_ids', $ids);
        }

        return $ids;
    }
}

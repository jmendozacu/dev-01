<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
/**
 * @method  int     getChildrenCount()
 * @method  $this   setChildrenCount(int $count)
 * @method  bool    getCreateDefaultPermission()
 * @method  $this   setCreateDefaultPermission(bool $flag)
 * @method  bool    getIncludeInMenu()
 * @method  $this   setIncludeInMenu(bool $flag)
 * @method  int     getLevel()
 * @method  $this   setLevel(int $level)
 * @method  bool    getManageVersions()
 * @method  $this   setManageVersions(bool $flag)
 * @method  int     getPageId()
 * @method  int     getParentId()
 * @method  $this   setParentId(int $parentId)
 * @method  string  getPath()
 * @method  $this   setPath(string $path)
 * @method  int     getPosition()
 * @method  $this   setPosition(int $position)
 * @method  int     getStoreId()
 * @method  $this   setStoreId(int $storeId)
 * @method  array   getStores()
 * @method  $this   setStores(array $stores)
 * @method  string  getUrlKey()
 * @method  $this   setUrlKey(string $urlKey)
 */
class Bubble_CmsTree_Model_Cms_Page extends Mage_Cms_Model_Page
{
    /**
     * Duplicate current page to specified store
     *
     * @param int $storeId
     * @param null|int $parentId
     * @param bool $withChildren
     * @return $this
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function duplicate($storeId = 0, $parentId = null, $withChildren = true)
    {
        try {
            $this->_getResource()->beginTransaction();
            $page = clone $this;

            if (!$this->getParentId()) {
                // If page to duplicate is the root page, duplicate it under itself
                $this->setParentId($this->getId());
            }

            if ($storeId > 0) {
                // If page is duplicated on a specific store, 'stores' key is not needed
                $this->unsetData('stores');
            }

            if ($storeId != $this->getStoreId()) {
                if (null === $parentId) {
                    // If we are duplicating on another store, copy it under root page (create root if it does not exist)
                    $parent = Mage::getModel('cms/page')->loadRootByStoreId($storeId);
                    if (!$parent->getId()) {
                        $parent = self::createDefaultStoreRootPage($storeId);
                    }
                    if (!$parent->getId()) {
                        Mage::throwException(
                            Mage::helper('bubble_cmstree')->__('Could not create root page of store %s', $storeId)
                        );
                    }
                    $parentId = $parent->getId();
                }

                $this->setLevel(2);
                $this->setSortOrder(0);
            } elseif (null === $parentId) { // Only on root duplicated page
                // Avoid same identifiers in the same tree
                $this->setTitle($this->getTitle() . Mage::helper('bubble_cmstree')->__(' (copy)'));
            }

            if (null !== $parentId) {
                $this->setParentId($parentId);
            }

            $this->setStoreId($storeId);
            $this->setChildrenCount(0);

            // Unset some data that will be auto-generated
            $this->unsetData('creation_time');
            $this->unsetData('update_time');
            $this->unsetData('page_id');
            $this->unsetData('path');
            $this->unsetData('identifier');
            $this->unsetData('url_key');

            $this->save();

            if ($withChildren) {
                foreach ($page->getChildren() as $child) {
                    /** @var Bubble_CmsTree_Model_Cms_Page $child */
                    $child->duplicate($storeId, $this->getId());
                }
            }

            $this->_getResource()->commit();

        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        
        return $this;
    }
    
    /**
     * @param int $parentId
     * @param int $afterPageId
     * @return $this
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function move($parentId, $afterPageId)
    {
        /**
         * Validate new parent page id. (page model is used for backward
         * compatibility in event params)
         */
        $parent = Mage::getModel('cms/page')
            ->setStores(array($this->getStoreId()))
            ->load($parentId);

        if (!$parent->getId()) {
            Mage::throwException(
                Mage::helper('cms')->__('Page move operation is not possible: the new parent page was not found.')
            );
        }

        $moveComplete = false;

        $eventParams = array(
            $this->_eventObject => $this,
            'parent'            => $parent,
            'page_id'           => $this->getId(),
            'prev_parent_id'    => $this->getParentId(),
            'parent_id'         => $parentId
        );

        try {
            $this->_getResource()->beginTransaction();
            Mage::dispatchEvent('cms_page_tree_move_before', $eventParams);
            Mage::dispatchEvent($this->_eventPrefix . '_move_before', $eventParams);

            $this->getResource()->changeParent($this, $parent, $afterPageId);

            Mage::dispatchEvent($this->_eventPrefix . '_move_after', $eventParams);
            Mage::dispatchEvent('cms_page_tree_move_after', $eventParams);

            $this->_getResource()->commit();

            $moveComplete = true;
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }

        if ($moveComplete) {
            Mage::dispatchEvent('page_move', $eventParams);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), array($this->getId()));
    }

    /**
     * @return array
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }

        return $ids;
    }

    /**
     * @return Bubble_CmsTree_Model_Cms_Page
     */
    public function getParentPage()
    {
        if (!$this->hasData('parent_page')) {
            $this->setData('parent_page', Mage::getModel('cms/page')->load($this->getParentId()));
        }

        return $this->_getData('parent_page');
    }

    /**
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        $str = Mage::helper('core')->removeAccents($str);
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $str);
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function loadRootByStoreId($storeId)
    {
        $rootId = $this->_getResource()->getStoreRootId($storeId);
        if ($rootId) {
            $this->load($rootId);
        }

        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getChildren()
    {
        return $this->getCollection()->addChildrenFilter($this);
    }

    /**
     * @param mixed $store
     * @return string
     */
    public function getUrl($store = null)
    {
        $identifier = $this->getIdentifier();
        $suffix = Mage::helper('bubble_cmstree')->getUrlSuffix($store);
        if (strlen($suffix)) {
            $identifier .= $suffix;
        }

        if (!Mage::app()->getStore()->isAdmin()) {
            return Mage::getBaseUrl() . $identifier;
        }

        $store = Mage::app()->getStore($store);

        return $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $identifier;
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        if (is_null($this->getParentId())) {
            return false;
        }

        return 0 === (int) $this->getParentId();
    }

    /**
     * @param int $storeId
     * @param array $data
     * @return Bubble_CmsTree_Model_Cms_Page
     */
    public static function createDefaultStoreRootPage($storeId, $data = array())
    {
        $newRoot = Mage::getModel('cms/page')->setData(array(
            'title'         => Mage::helper('cms')->__('Home'),
            'root_template' => 'two_columns_right',
            'store_id'      => $storeId,
            'parent_id'     => 0,
            'level'         => 1,
        ))
        ->addData($data) // will override default data
        ->setCreateDefaultPermission(true)
        ->save();

        return $newRoot;
    }

    /**
     * @return $this
     */
    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        $this->getResource()->decreaseChildrenCount($this, $this->getParentIds());

        return $this;
    }

    /**
     * @return $this
     */
    protected function _afterDelete()
    {
        parent::_afterDelete();
        $this->getResource()->deleteChildren($this);

        return $this;
    }

    /**
     * @return $this
     */
    protected function _beforeSave()
    {
        if ($this->isRoot()) {
            $this->setIdentifier('');
        } else {
            if ($this->getPageId()) {
                // Edit existant page
                $identifiers = explode('/', $this->getIdentifier());
                array_pop($identifiers);
            } else {
                // Add new page
                $parent = $this->getParentPage();
                $identifiers = explode('/', $parent->getIdentifier());
                $this->getResource()->increaseChildrenCount($this, $parent->getPathIds());
                $this->setCreateDefaultPermission(true);
            }
            $identifier = $this->getUrlKey() ? $this->getUrlKey() : $this->getTitle();
            array_push($identifiers, $this->formatUrlKey($identifier));
            $this->setIdentifier(trim(implode('/', $identifiers), '/'));
        }

        parent::_beforeSave();

        // old page data
        $oldPage = Mage::getModel('cms/page')->load($this->getPageId());

        if (!$this->isRoot() && Mage::helper('cms/page')->isCreatePermanentRedirects($this->getStoreId())) {
            // 301 Redirects
            $this->getResource()->updatePermanentRedirects($oldPage, $this);
        }

        $this->getResource()->updateChildrenIdentifiers($oldPage, $this->getIdentifier());

        return $this;
    }

    /**
     * @return $this
     */
    protected function _afterSave()
    {
        if (!$this->getPath() && $this->getPageId()) {
            if (!$this->getPosition()) {
                $this->setPosition($this->getPageId());
            }
            $path = '';
            if ($this->getParentId()) {
                $parent = $this->getParentPage();
                $path = $parent->getPath();
            }
            $path .= '/' . $this->getPageId();
            $path = trim($path, '/');
            $this->setPath($path)
                ->setLevel(count(explode('/', $path)))
                ->save();
        }

        if ($this->getCreateDefaultPermission()) {
            $storeId = $this->getStoreId();
            $customerGroupId = Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
            $resourceModel = Mage::getResourceModel('cms/page_permission');
            if ($this->isRoot() || $resourceModel->exists($storeId, $customerGroupId, $this->getParentId())) {
                Mage::getModel('cms/page_permission')->addData(array(
                    'store_id' => $storeId,
                    'customer_group_id' => $customerGroupId,
                    'page_id' => $this->getPageId(),
                ))
                ->save();
            }
            $this->setCreateDefaultPermission(false);
        }

        return parent::_afterSave();
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $identifiers = explode('/', $this->getIdentifier());
        $this->setUrlKey(array_pop($identifiers));

        return $this;
    }
}

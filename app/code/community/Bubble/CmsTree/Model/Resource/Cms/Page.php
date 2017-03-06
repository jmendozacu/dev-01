<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Model_Resource_Cms_Page extends Mage_Cms_Model_Mysql4_Page
{
    /**
     * @var bool
     */
    protected $_tableTreeStoreExists;

    /**
     * Initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $tablePrefix = Mage::getConfig()->getTablePrefix();
        Mage::getSingleton('core/resource')->setMappedTableName('cms_page', $tablePrefix . 'bubble_cms_page_tree');
        if ($this->_tableTreeStoreExists()) {
            Mage::getSingleton('core/resource')->setMappedTableName(
                'cms_page_store',
                $tablePrefix . 'bubble_cms_page_tree_store'
            );
        }
    }

    /**
     * @param Mage_Cms_Model_Page $page
     * @param Mage_Cms_Model_Page $newParent
     * @param null|int $afterPageId
     * @return $this
     */
    public function changeParent(Mage_Cms_Model_Page $page, Mage_Cms_Model_Page $newParent, $afterPageId = null)
    {
        $table = $this->getTable('cms/page');
        $adapter = $this->_getWriteAdapter();

        // Decrease children count for all old page parent pages
        $this->decreaseChildrenCount($page, $page->getParentIds());

        // Increase children count for new page parents
        $this->increaseChildrenCount($page, $newParent->getPathIds());

        $position = $this->_processPositions($page, $newParent, $afterPageId);
        $newPath = $newParent->getPath() . '/' . $page->getId();
        $identifiers = explode('/', $page->getIdentifier());
        $newIdentifier = trim($newParent->getIdentifier() . '/' . array_pop($identifiers), '/');
        $newLevel = $newParent->getLevel() + 1;
        $levelDisposition = $newLevel - $page->getLevel();

        if (Mage::helper('cms/page')->isCreatePermanentRedirects($page->getStoreId())) {
            $newPage = clone $page;
            $newPage->setIdentifier($newIdentifier);
            $this->updatePermanentRedirects($page, $newPage);
        }

        // Update children nodes identifiers
        $this->updateChildrenIdentifiers($page, $newIdentifier);

        // Update children nodes path
        $sql = "UPDATE {$table} SET
            `path`  = REPLACE(`path`, '{$page->getPath()}/', '{$newPath}/'),
            `level` = `level` + {$levelDisposition}
            WHERE ". $adapter->quoteInto('path LIKE ?', $page->getPath() . '/%');
        $adapter->query($sql);

        // Update moved page data
        $data = array(
        	'path'       => $newPath,
        	'level'      => $newLevel,
        	'identifier' => $newIdentifier,
            'position'   => $position,
            'parent_id'  => $newParent->getId()
        );
        $adapter->update($table, $data, $adapter->quoteInto('page_id = ?', $page->getId()));

        // Update page object to new data
        $page->addData($data);

        return $this;
    }

    /**
     * @param Mage_Cms_Model_Page $page
     * @param array $pageIds
     */
    public function decreaseChildrenCount(Mage_Cms_Model_Page $page, $pageIds)
    {
        $this->_updateChildrenCount($page, $pageIds, '-');
    }

    /**
     * @param Mage_Cms_Model_Page $page
     * @param array $pageIds
     */
    public function increaseChildrenCount(Mage_Cms_Model_Page $page, $pageIds)
    {
        $this->_updateChildrenCount($page, $pageIds, '+');
    }

    /**
     * @param Mage_Cms_Model_Page $page
     * @param string $newIdentifier
     * @return $this
     */
    public function updateChildrenIdentifiers(Mage_Cms_Model_Page $page, $newIdentifier)
    {
        $table = $this->getTable('cms/page');
        $adapter = $this->_getWriteAdapter();
        $oldIdentifier = str_replace('/', '\/', $page->getIdentifier());
        $children = Mage::getModel('cms/page')->getCollection()
            ->addFieldToFilter('path', array('like' => $page->getPath().'/%'));
        foreach ($children as $child) {
            $identifier = preg_replace("/^{$oldIdentifier}\/(.*)/i", "{$newIdentifier}/\$1", $child->getIdentifier());
            $sql = "UPDATE {$table} SET "
                . $adapter->quoteInto('identifier = ? ', $identifier)
                . $adapter->quoteInto('WHERE page_id = ?', $child->getPageId());
            $adapter->query($sql);
        }

        return $this;
    }

    /**
     * @param int $pageId
     * @return int
     */
    public function getChildrenCount($pageId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('cms/page'), 'children_count')
            ->where('page_id = ?', $pageId);

        $child = $this->_getReadAdapter()->fetchOne($select);

        return (int) $child;
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getStoreRootId($storeId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('cms/page'), 'page_id')
            ->where('parent_id = 0')
            ->where('store_id = ?', $storeId);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * @param string $identifier
     * @param int $storeId
     * @return string
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $select = $this->_getReadAdapter()->select()->from(array('main_table' => $this->getMainTable()), 'page_id')
            ->where('main_table.identifier = ?', $identifier)
            ->where('main_table.is_active = 1 AND main_table.store_id IN (0, ?)', $storeId)
            ->order('main_table.store_id DESC');

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * @param string $identifier
     * @return string
     */
    public function getCmsPageTitleByIdentifier($identifier)
    {
        /* @var $select Zend_Db_Select */
        $select = $this->_getReadAdapter()->select();
        $select->from(array('main_table' => $this->getMainTable()), 'title')
            ->where('main_table.identifier = ?', $identifier)
            ->where('main_table.store_id = ?', $this->getStore()->getId())
            ->order('main_table.store_id DESC');

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniquePageToStores(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getWriteAdapter()->select()
                ->from($this->getMainTable())
                ->where($this->getMainTable().'.identifier = ?', $object->getData('identifier'))
                ->where($this->getMainTable().'.store_id = ?', $object->getStoreId());
        if ($object->getId()) {
            $select->where($this->getMainTable().'.page_id <> ?',$object->getId());
        }

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     * @param Mage_Cms_Model_Page $oldPage
     * @param Mage_Cms_Model_Page $newPage
     * @throws Exception
     */
    public function updatePermanentRedirects(Mage_Cms_Model_Page $oldPage, Mage_Cms_Model_Page $newPage)
    {
        $this->deletePermanentRedirects($newPage);

        $children = $newPage->getCollection()
            ->addAllChildrenFilter($oldPage);
        $children->addItem($oldPage); // 301 redirect for me too
        foreach ($children as $child) {
            $newIdentifier = str_replace($oldPage->getIdentifier(), $newPage->getIdentifier(), $child->getIdentifier());
            if ($newIdentifier != $child->getIdentifier()) {
                $data = array(
                    'request_path' => $child->getIdentifier(),
                    'target_path'  => $newIdentifier,
                    'id_path'      => microtime(),
                    'store_id'     => $newPage->getStoreId(),
                    'is_system'    => 0,
                    'options'      => 'RP',
                );
                Mage::getModel('core/url_rewrite')
                    ->setStoreId($newPage->getStoreId())
                    ->loadByRequestPath($child->getIdentifier())
                    ->addData($data)
                    ->save();
            }
        }
    }

    /**
     * @param Mage_Cms_Model_Page $page
     * @throws Exception
     */
    public function deletePermanentRedirects(Mage_Cms_Model_Page $page)
    {
        $urls = Mage::getModel('core/url_rewrite')->getCollection()
            ->addFieldToFilter('store_id', $page->getStoreId())
            ->addFieldToFilter('request_path', array('like' => $page->getIdentifier() . '%'));
        foreach ($urls as $url) {
            $url->delete(); // Removing old url to avoid infinite loop
        }
    }

    /**
     * @param Mage_Cms_Model_Page $page
     */
    public function deleteChildren(Mage_Cms_Model_Page $page)
    {
        $table = $this->getTable('cms/page');
        $adapter = $this->_getWriteAdapter();
        $path = $page->getPath();
        $sql = "DELETE FROM {$table} WHERE path LIKE '{$path}/%'";
        $adapter->query($sql);
    }

    /**
     * @param Mage_Cms_Model_Page $page
     * @param array $pageIds
     * @param string$operator
     */
    protected function _updateChildrenCount(Mage_Cms_Model_Page $page, $pageIds, $operator)
    {
        $table = $this->getTable('cms/page');
        $childrenCount = $this->getChildrenCount($page->getId()) + 1;
        $adapter = $this->_getWriteAdapter();
        $adapter->query("SET sql_mode = 'NO_UNSIGNED_SUBTRACTION';"); // avoid error if using SIGNED result
        $sql = "UPDATE {$table} SET children_count = children_count {$operator} {$childrenCount} WHERE page_id IN (?)";
        $adapter->query($adapter->quoteInto($sql, $pageIds));
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return bool|int
     */
    protected function isValidPageIdentifier(Mage_Core_Model_Abstract $object)
    {
        // Homepage case
        if (!$object->getParentId() && $object->getData('identifier') === '') {
            return true;
        }

        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return $this
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        Mage::app()->cleanCache(array('CATALOG_NAVIGATION'));

        if ($object->getStoreId() === '0' && $this->_tableTreeStoreExists()) {
            return parent::_afterSave($object);
        }

        return $this;
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return $this
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId() && $this->_tableTreeStoreExists()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('stores', $stores);
        }

        return $this;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param Mage_Cms_Model_Page $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where($this->getMainTable() . '.' . $field . ' = ?', $value);

        return $select;
    }

    /**
     * @param Bubble_CmsTree_Model_Cms_Page $page
     * @param Bubble_CmsTree_Model_Cms_Page $newParent
     * @param int $afterPageId
     * @return int|string
     */
    protected function _processPositions($page, $newParent, $afterPageId)
    {
        $table = $this->getTable('cms/page');
        $adapter = $this->_getWriteAdapter();

        $sql = "UPDATE {$table} SET `position` = `position` - 1 WHERE "
            . $adapter->quoteInto('parent_id = ? AND ', $page->getParentId())
            . $adapter->quoteInto('position > ?', $page->getPosition());
        $adapter->query($sql);

        // Prepare position value
        if ($afterPageId) {
            $sql = "SELECT `position` FROM {$table} WHERE page_id = ?";
            $position = $adapter->fetchOne($adapter->quoteInto($sql, $afterPageId));
            $sql = "UPDATE {$table} SET `position` = `position` + 1 WHERE "
                . $adapter->quoteInto('parent_id = ? AND ', $newParent->getId())
                . $adapter->quoteInto('position > ?', $position);
            $adapter->query($sql);
        } elseif ($afterPageId !== null) {
            $position = 0;
            $sql = "UPDATE {$table} SET `position` = `position` + 1 WHERE "
                . $adapter->quoteInto('parent_id = ? AND ', $newParent->getId())
                . $adapter->quoteInto('position > ?', $position);
            $adapter->query($sql);
        } else {
            $sql = "SELECT MIN(`position`) FROM {$table} WHERE parent_id = ?";
            $position = $adapter->fetchOne($adapter->quoteInto($sql, $newParent->getId()));
        }
        $position += 1;

        return $position;
    }

    /**
     * @return bool
     */
    protected function _tableTreeStoreExists()
    {
        if (null === $this->_tableTreeStoreExists) {
            $this->_tableTreeStoreExists = $this->getReadConnection()
                ->isTableExists(Mage::getConfig()->getTablePrefix() . 'bubble_cms_page_tree_store');
        }

        return $this->_tableTreeStoreExists;
    }
}

<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Model_Resource_Cms_Page_Permission extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialization
     */
    protected function _construct()
    {
        $this->_init('cms/page_permission', 'permission_id');
    }

    /**
     * @param int $storeId
     * @param int $customerGroupId
     * @return array
     */
    public function getPagesByStoreAndCustomerGroup($storeId, $customerGroupId)
    {
        $select = $this->_getReadAdapter()
            ->select()
            ->from($this->getMainTable(), 'page_id')
            ->where('store_id = ?', $storeId)
            ->where('customer_group_id = ?', $customerGroupId);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * @param int $storeId
     * @param int $customerGroupId
     * @param array $pages
     * @return $this
     */
    public function savePermissions($storeId, $customerGroupId, $pages)
    {
        $adapter = $this->_getWriteAdapter();

        $adapter->delete(
            $this->getMainTable(),
            $adapter->quoteInto('store_id = ?', $storeId) . ' AND '
            . $adapter->quoteInto('customer_group_id = ?', $customerGroupId));

        $insert = array();
        foreach ($pages as $pageId) {
            if ($pageId) {
                $insert[] = array(
                    'store_id' => $storeId,
                    'customer_group_id' => $customerGroupId,
                    'page_id' => $pageId,
                );
            }
        }
        if (!empty($insert)) {
            $adapter->insertMultiple($this->getMainTable(), $insert);
        }

        return $this;
    }

    /**
     * @param int $storeId
     * @param int $customerGroupId
     * @param int $pageId
     * @return array
     */
    public function exists($storeId, $customerGroupId, $pageId)
    {
        $select = $this->_getReadAdapter()
            ->select()
            ->from($this->getMainTable())
            ->where('store_id IN (0, ?)', $storeId)
            ->where('customer_group_id = ?', $customerGroupId)
            ->where('page_id = ?', $pageId);

        return $this->_getReadAdapter()->fetchRow($select);
    }
}
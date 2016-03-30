<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.3.3.1
 * @build     1291
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_SearchIndex_Model_Catalogsearch_Resource_Fulltext_Collection extends Mage_CatalogSearch_Model_Resource_Fulltext_Collection
{
    public function addSearchFilter($query)
    {
        $catalogIndex = Mage::helper('searchindex/index')->getIndex('mage_catalog_product');
        $catalogIndex->joinMatched($this);

        $this->_addStockOrder($this);

        if (Mage::getSingleton('catalog/session')->getSortOrder() == ''
            && Mage::getSingleton('catalog/session')->getSortDirection() == '') {
            $this->setOrder('relevance');
        }

        return $this;
    }

    protected function _addStockOrder($collection)
    {
        $index = Mage::helper('searchindex/index')->getIndex('mage_catalog_product');

        if ($index->getProperty('out_of_stock_to_end')) {
            $resource = Mage::getSingleton('core/resource');
            $select = $collection->getSelect();

            $select->joinLeft(
                array('ss_inventory_table' => $resource->getTableName('cataloginventory_stock_item')),
                'ss_inventory_table.product_id = e.entity_id',
                array('is_in_stock', 'manage_stock')
            );

            $select->order(new Zend_Db_Expr('(CASE WHEN (((ss_inventory_table.use_config_manage_stock = 1)
                AND (ss_inventory_table.is_in_stock = 1)) OR  ((ss_inventory_table.use_config_manage_stock = 0)
                AND (1 - ss_inventory_table.manage_stock + ss_inventory_table.is_in_stock >= 1)))
                THEN 1 ELSE 0 END) DESC'));
        }

        return $this;
    }
		
		protected function _addUrlRewrite()
		{
			/** @var Amasty_SeoSingleUrl_Helper_Data $helper */
			$helper = Mage::helper('amseourl');
			if (Amasty_SeoSingleUrl_Helper_Data::urlRewriteHelperEnabled() || $helper->useDefaultProductUrlRules()) {
				parent::_addUrlRewrite();
			} else {
				$urlRewrites = null;
				if ($this->_cacheConf) {
					if (! ($urlRewrites = Mage::app()->loadCache($this->_cacheConf['prefix'] . 'urlrewrite'))) {
						$urlRewrites = null;
					} else {
						$urlRewrites = unserialize($urlRewrites);
					}
				}

				if (! $urlRewrites) {
					$productIds = array();
					foreach ($this->getItems() as $item) {
						$productIds[] = $item->getEntityId();
					}
					if (! count($productIds)) {
						return;
					}

					/** @var Amasty_SeoSingleUrl_Helper_Product_Url_Rewrite $helper */
					$helper  = Mage::helper('amseourl/product_url_rewrite');
					$storeId = $this->getStoreId() ? $this->getStoreId() : Mage::app()->getStore()->getId();
					$select  = $helper
						->getTableSelect($productIds, $this->_urlRewriteCategory, $storeId);

					foreach ($this->getConnection()->fetchAll($select) as $row) {
						if (! isset($urlRewrites[$row['product_id']])) {
							$urlRewrites[$row['product_id']] = $row['request_path'];
						}
					}

					if ($this->_cacheConf) {
						Mage::app()->saveCache(
							serialize($urlRewrites),
							$this->_cacheConf['prefix'] . 'urlrewrite',
							array_merge($this->_cacheConf['tags'], array(Mage_Catalog_Model_Product_Url::CACHE_TAG)),
							$this->_cacheLifetime
						);
					}
				}

				foreach ($this->getItems() as $item) {
					if (isset($urlRewrites[$item->getEntityId()])) {
						$item->setData('request_path', $urlRewrites[$item->getEntityId()]);
					} else {
						$item->setData('request_path', false);
					}
				}
			}
		}
}

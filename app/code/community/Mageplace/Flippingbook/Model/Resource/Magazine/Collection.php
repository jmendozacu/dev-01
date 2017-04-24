<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Model_Resource_Magazine_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_productId;

    protected function _construct()
    {
        $this->_init('flippingbook/magazine');
    }

    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();

        $countSelect->reset(Zend_Db_Select::GROUP);

        return $countSelect;
    }

    public function setProductId($product_id)
    {
        $this->_productId = $product_id;

        return $this;
    }

    protected function _afterLoad()
    {

        $magazines = $this->getColumnValues('magazine_id');
        if (count($magazines)) {
            $select = $this->getConnection()
                ->select()
                ->from(
                    $this->getTable('flippingbook/magazine_store')
                )->where(
                    $this->getTable('flippingbook/magazine_store') . '.magazine_id IN (?)',
                    $magazines
                );

            if ($result = $this->getConnection()->fetchPairs($select)) {
                foreach ($this as $item) {
                    $magazine_id = $item->getData('magazine_id');
                    if (!isset($result[$magazine_id])) {
                        continue;
                    }
                    if ($result[$magazine_id] == 0) {
                        $stores    = Mage::app()->getStores(false, true);
                        $storeId   = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId   = $result[$magazine_id];
                        $storeCode = Mage::app()->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                }
            }
        }


        if ($this->_productId) {
            foreach ($this->_items as $item) {
                if ($item->getData('entity_id') == $this->_productId) {
                    $item->setData('product_attached', 1);
                } else {
                    $item->setData('product_attached', 0);
                }
            }
        }

        return parent::_afterLoad();
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinTables();
        return $this;
    }

    protected function _joinTables()
    {
        $this->getSelect()
            ->joinLeft(
                array(
                    'store_table' => $this->getTable('flippingbook/magazine_store')
                ),
                'main_table.magazine_id = store_table.magazine_id',
                array('store_id')
            )->joinLeft(
                array(
                    'product_magazine_table' => $this->getTable('flippingbook/product_magazine')
                ),
                'main_table.magazine_id = product_magazine_table.magazine_id',
                array('entity_id')
            )->joinLeft(
                array(
                    'resolution_table' => $this->getTable('flippingbook/resolution')
                ),
                'main_table.magazine_resolution_id = resolution_table.resolution_id',
                array('resolution_width', 'resolution_height')
            )->group(
                'main_table.magazine_id'
            );

        return $this;
    }


    public function toOptionHash()
    {
        return $this->_toOptionHash('magazine_id', 'magazine_title');
    }


    public function toOptionArray()
    {
        return $this->_toOptionArray('magazine_id', 'magazine_title');
    }

    public function addIsActiveFilter()
    {
        $this->addFilter('main_table.is_active', 1);

        return $this;
    }

    public function setOrderByPosition($direction = parent::SORT_ORDER_ASC)
    {
        return $this->addOrder('main_table.magazine_sort_order', $direction);
    }


    public function addMagazineIdFilter($magazine_id)
    {
        if (is_array($magazine_id)) {
            if (array_key_exists('from', $magazine_id)) {
                $this->getSelect()->where('main_table.magazine_id >= ?', intval($magazine_id['from']));
            }

            if (array_key_exists('to', $magazine_id)) {
                $this->getSelect()->where('main_table.magazine_id <= ?', intval($magazine_id['to']));
            }

        } else {
            $this->addFilter('main_table.magazine_id', intval($magazine_id));
        }

        return $this;
    }


    public function addProductAttachedFilter($state)
    {
        if ($state) {
            $this->getSelect()->where('product_magazine_table.entity_id = ?', $this->_productId);
        } else {
            $this->getSelect()->where('product_magazine_table.entity_id IS NULL');
        }

        return $this;
    }


    public function addStoreFilter($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = $store->getId();
        }

        $store = (int)$store;

        if ($store) {
            $this->getSelect()
                ->where(
                    'store_table.store_id IN (?)',
                    array(
                        0,
                        $store
                    )
                );
        }

        return $this;
    }

    public function addCategoryFilter($category)
    {
        if ($category instanceof Mageplace_Flashmagazine_Model_Category) {
            $category = $category->getId();
        } else if ($category instanceof Mageplace_Flashmagazine_Model_Magazine) {
            $category = $category->getMagazineCategoryId();
        }

        $category = (int)$category;

        $select = $this->getSelect()
            ->join(
                array(
                    'category_table' => $this->getTable('flippingbook/category')
                ),
                'main_table.magazine_category_id = category_table.category_id',
                array()
            )
            ->where(
                'category_table.category_id IN (?)',
                array(
                    0,
                    $category
                )
            )->group(
                'main_table.magazine_category_id'
            );


        return $this;
    }


    public function addTemplateFilter($template)
    {
        if ($template instanceof Mageplace_Flippingbook_Model_Template) {
            $template = $template->getId();
        } else if ($template instanceof Mageplace_Flippingbook_Model_Magazine) {
            $template = $template->getMagazineTemplateId();
        }

        $template = (int)$template;

        $select = $this->getSelect()
            ->join(
                array(
                    'template_table' => $this->getTable('flippingbook/template')
                ),
                'main_table.magazine_template_id = template_table.template_id',
                array()
            )
            ->where(
                'template_table.template_id IN (?)',
                array(
                    0,
                    $template
                )
            )->group(
                'main_table.magazine_template_id'
            );

        return $this;
    }


    public function addResolutionFilter($resolution)
    {
        if ($resolution instanceof Mageplace_Flippingbook_Model_Resolution) {
            $resolution = $resolution->getId();
        } else if ($resolution instanceof Mageplace_Flippingbook_Model_Magazine) {
            $resolution = $resolution->getMagazineResolutionId();
        }

        $resolution = (int)$resolution;

        $select = $this->getSelect()
            ->where(
                'resolution_table.resolution_id IN (?)',
                array(
                    0,
                    $resolution
                )
            )->group(
                'main_table.magazine_resolution_id'
            );

        return $this;
    }

}
<?php

/**
 * Created by PhpStorm.
 * User: Rekindle
 * Date: 5/16/2016
 * Time: 10:54 AM
 */
class Magebuzz_Customwishlist_Block_Adminhtml_Customer_Wishlistgrid_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customwishlist_grid');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('wishlist/item_collection')->join(
            array('wishlist_table' => 'wishlist'), 'main_table.wishlist_id = wishlist_table.wishlist_id', array('wishlist_table.customer_id')
        );
        $collection->getSelect()->join(
            array('customer' => 'customer_entity'), 'customer.entity_id = wishlist_table.customer_id', array('customer.email')
        );
        /*create subquery to get customer name (using Zend_Db_Expr )-> join with maintable*/
        $query_customer_name = "SELECT
                    FIRST .entity_id,
                    CONCAT(FIRST . VALUE, ' ', last. VALUE) as customer_name
                    FROM
                        `customer_entity_varchar` FIRST
                    INNER JOIN customer_entity_varchar last ON FIRST .entity_id = last.entity_id
                    AND last.attribute_id = 7
                    WHERE
                    FIRST .attribute_id = 5";

        $collection->getSelect()->joinLeft(array('subtable_customer' => new Zend_Db_Expr('(' . $query_customer_name . ')')), 'subtable_customer.entity_id = wishlist_table.customer_id', array('subtable_customer.customer_name'));

        $query_product_name = " SELECT
                        `value` AS product_name, entity_id
                    FROM
                        catalog_product_entity_varchar
                    WHERE
                        entity_type_id = (
                            SELECT
                                entity_type_id
                            FROM
                                eav_entity_type
                            WHERE
                                entity_type_code = 'catalog_product'
                        )
                    AND attribute_id = (
                        SELECT
                            attribute_id
                        FROM
                            eav_attribute
                        WHERE
                            attribute_code = 'name'
                        AND entity_type_id = (
                            SELECT
                                entity_type_id
                            FROM
                                eav_entity_type
                            WHERE
                                entity_type_code = 'catalog_product'
                        )
                    )";

        $collection->getSelect()->joinLeft(array('subtable_product' => new Zend_Db_Expr('(' . $query_product_name . ')')), 'subtable_product.entity_id = main_table.product_id', array('subtable_product.product_name'));
        $collection->getSelect()->group('main_table.wishlist_item_id');
        //Mage::log($collection->getSelect()->__toString());
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('customwishlist');

        $this->addColumn('customer_name', array(
            'header' => Mage::helper('customer')->__('Customer Name'),
            'width' => '150',
            'index' => 'customer_name',
        ));
        $this->addColumn('email', array(
            'header' => Mage::helper('customer')->__('Email'),
            'width' => '150',
            'index' => 'email'
        ));
        $this->addColumn('product_name', array(
            'header' => Mage::helper('customer')->__('Product Name'),
            'width' => '150',
            'index' => 'product_name',
        ));
        $this->addColumn('qty', array(
            'header' => Mage::helper('customer')->__('Qty'),
            'width' => '150',
            'index' => 'qty'
        ));
        $this->addColumn('added_at', array(
            'header' => Mage::helper('customer')->__('Added At'),
            'type' => 'datetime',
            'width' => '120',
            'index' => 'added_at'
        ));
        $this->addExportType('*/*/exportWishlistCsv', $helper->__('CSV'));
        $this->addExportType('*/*/exportWishlistExcel', $helper->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
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
        //Mage::log($collection2->getSelect()->__toString());
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('customwishlist');

        $this->addColumn('customer_id', array(
            'header' => Mage::helper('customer')->__('Customer Name'),
            'width' => '150',
            'sortable'  => false,
            'renderer' => 'Magebuzz_Customwishlist_Block_Adminhtml_Renderer_CustomerName',
            'filter_condition_callback' => array($this,'_customerNameFilter')
        ));
        $this->addColumn('email', array(
            'header' => Mage::helper('customer')->__('Email'),
            'width' => '150',
            'index' => 'email'
        ));
        $this->addColumn('product_id', array(
            'header' => Mage::helper('customer')->__('Product Name'),
            'width' => '150',
            'sortable'  => false,
            'renderer' => 'Magebuzz_Customwishlist_Block_Adminhtml_Renderer_ProductName',
            'filter_condition_callback' => array($this,'_productNameFilter')
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

    public function _customerNameFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = "select entity_id from customer_entity_varchar WHERE customer_entity_varchar.value like '%" . $value . "%'";
        $results = $readConnection->fetchAll($query);

        $customer_ids = array();
        foreach ($results as $item) {
            array_push($customer_ids, $item['entity_id']);
        }
        json_encode($customer_ids);

        $customer_ids_str = str_replace(array('[',']'),array('(',')'),json_encode($customer_ids));

        $this->getCollection()->getSelect()
            ->where("customer.entity_id IN " .$customer_ids_str);
        return $this;
    }
    public function _productNameFilter($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = "select entity_id from catalog_product_entity_varchar WHERE catalog_product_entity_varchar.value like '%" . $value . "%'";
        $results = $readConnection->fetchAll($query);
        $product_ids = array();
        foreach ($results as $item) {
            array_push($product_ids, $item['entity_id']);
        }
        json_encode($product_ids);

        $product_ids_str = str_replace(array('[',']'),array('(',')'),json_encode($product_ids));
        $this->getCollection()->getSelect()
            ->where("main_table.product_id IN ".$product_ids_str);
        return $this;

    }
}
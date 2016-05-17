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
            array('wishlist_table'=>'wishlist'),'main_table.wishlist_id=wishlist_table.wishlist_id',array('wishlist_table.customer_id')
        );
        $collection->getSelect()->join(
            array('customer'=>'customer_entity'),'customer.entity_id = wishlist_table.customer_id',array('customer.email')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('customwishlist');
        $this->addColumn('customer_id', array(
            'header'    => Mage::helper('customer')->__('Customer Name'),
            'width'     => '150',
            'renderer'     => 'Magebuzz_Customwishlist_Block_Adminhtml_Renderer_CustomerName',
            'filter_condition_callback' => array($this,'_customerNameFilter'),
        ));
        $this->addColumn('product_id', array(
            'header'    => Mage::helper('customer')->__('Product Name'),
            'width'     => '150',
            'renderer'     => 'Magebuzz_Customwishlist_Block_Adminhtml_Renderer_ProductName'
        ));
        $this->addColumn('qty', array(
            'header'    => Mage::helper('customer')->__('Qty'),
            'width'     => '150',
            'index'     => 'qty'
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => '150',
            'index'     => 'email'
        ));
        $this->addColumn('added_at', array(
            'header'    => Mage::helper('customer')->__('Added At'),
            'type'  => 'datetime',
            'width'     => '120',
            'index'     => 'added_at'
        ));
        $this->addExportType('*/*/exportWishlistCsv', $helper->__('CSV'));
        $this->addExportType('*/*/exportWishlistExcel', $helper->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    public function _customerNameFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $this->getCollection()->getSelect()->where(
            "customer_entity_varchar.value like ?"
            , "%$value%");
        return $this;

    }
}
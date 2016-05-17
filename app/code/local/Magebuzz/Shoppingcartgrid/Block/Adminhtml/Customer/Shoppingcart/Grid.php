<?php
/**
 * Created by PhpStorm.
 * User: Rekindle
 * Date: 5/16/2016
 * Time: 10:54 AM
 */

class Magebuzz_Shoppingcartgrid_Block_Adminhtml_Customer_Shoppingcart_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customwishlist_grid');
        $this->setSaveParametersInSession(true);

    }

    protected function _prepareCollection()    {
        $storeIds = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();
        $quote = Mage::getModel('sales/quote')->setSharedStoreIds($storeIds);
        $collection = $quote->getCollection();
        $collection->getSelect()->join(array('sfqi'=>'sales_flat_quote_item'),'`sfqi`.`item_id` = `main_table`.`entity_id`',array('sfqi.sku','sfqi.name'));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    =>Mage::helper('customer')->__('ID#'),
            'width'     =>'20px',
            'align'     =>'right',
            'index'     =>'entity_id'
        ));
        $this->addColumn('customer_email', array(
            'header'    => Mage::helper('customer')->__('Customer Email'),
            'width'     => '150',
            'index'     => 'customer_email'
        ));
        $this->addColumn('name', array(
            'header'    =>Mage::helper('customer')->__('Product Name'),
            'width'     =>'150px',
            'index'     =>'name'

        ));
        $this->addColumn('sku', array(
        'header'    =>Mage::helper('customer')->__('Sku'),
        'width'     =>'50px',
        'index'     =>'sku'

        ));
        $this->addColumn('items_qty', array(
            'header'    =>Mage::helper('customer')->__('Qty'),
            'width'     =>'50px',
            'index'     =>'items_qty'

        ));
        $this->addColumn('created_at', array(
            'header'    =>Mage::helper('customer')->__('Create At'),
            'width'     =>'50px',
            'type'      =>'datetime',
            'index'     =>'created_at'

        ));
        $this->addColumn('updated_at', array(
            'header'    =>Mage::helper('customer')->__('Update At'),
            'width'     =>'50px',
            'type'      =>'datetime',
            'index'     =>'updated_at'

        ));
        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportShoppingCartCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportShoppingCartExcel', Mage::helper('customer')->__('Excel XML'));
        return parent::_prepareColumns();
    }


}
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
        $collection = $quote->getCollection()
            ->addFieldToSelect('entity_id','new_id')
            ->addFieldToSelect('items_qty')
            ->addFieldToSelect('customer_email')
            ->addFieldToSelect('customer_id')
            ->addFieldToSelect('updated_at')
            ->addFieldToSelect('created_at')
            ->addFieldToFilter('reserved_order_id',array('null'=>true))
            ->addFieldToFilter('customer_email',array('notnull'=>true))
        ;
        $collection->getSelect()->join(array('sfqi'=>'sales_flat_quote_item'),'`sfqi`.`quote_id` = `main_table`.`entity_id`',array('sfqi.sku','sfqi.name'));
        $this->setCollection($collection);
        Mage::log($collection->getSelect()->__toString());
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer_id', array(
            'header'    => Mage::helper('customer')->__('Customer Name'),
            'width'     => '150',
            'renderer'     => 'Magebuzz_Customwishlist_Block_Adminhtml_Renderer_CustomerName',
            'filter_condition_callback' => array($this,'_customerNameFilter'),
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

        $this->addExportType('*/*/exportShoppingCartCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportShoppingCartExcel', Mage::helper('customer')->__('Excel XML'));
        return parent::_prepareColumns();
    }


}
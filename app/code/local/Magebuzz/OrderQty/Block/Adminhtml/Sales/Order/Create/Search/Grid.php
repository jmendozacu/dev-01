<?php
class Magebuzz_OrderQty_Block_Adminhtml_Sales_Order_Create_Search_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
{
    protected function _prepareCollection()
    {
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection
            ->setStore($this->getStore())
            ->addAttributeToSelect($attributes)
            ->addAttributeToSelect('sku')
            ->addStoreFilter()
            ->addAttributeToFilter('type_id', array_keys(
                Mage::getConfig()->getNode('adminhtml/sales/order/create/available_product_types')->asArray()
            ))
            ->addAttributeToSelect('gift_message_available');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('sales')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('sales')->__('Product Name'),
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_product',
            'index'     => 'name'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('sales')->__('SKU'),
            'width'     => '80',
            'index'     => 'sku'
        ));

        $this->addColumn('qty1',
            array(
                'header'=> Mage::helper('sales')->__('Qty'),
                'width' => '100px',
                'type'  => 'number',
                'index' => 'qty',
            ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('sales')->__('Price'),
            'column_css_class' => 'price',
            'align'     => 'center',
            'type'      => 'currency',
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate'      => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index'     => 'price',
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_price',
        ));

        $this->addColumn('in_products', array(
            'header'    => Mage::helper('sales')->__('Select'),
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_products',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id',
            'sortable'  => false,
        ));

        $this->addColumn('qty', array(
            'filter'    => false,
            'sortable'  => false,
            'header'    => Mage::helper('sales')->__('Qty To Add'),
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_qty',
            'name'      => 'qty',
            'inline_css'=> 'qty',
            'align'     => 'center',
            'type'      => 'input',
            'validate_class' => 'validate-number',
            'width'     => '1',
        ));

        return $this;
    }
}
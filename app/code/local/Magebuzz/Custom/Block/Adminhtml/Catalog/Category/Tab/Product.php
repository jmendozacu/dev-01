<?php
 
class Magebuzz_Custom_Block_Adminhtml_Catalog_Category_Tab_Product extends Mage_Adminhtml_Block_Catalog_Category_Tab_Product {

  protected function _getStore()
  {
    $storeId = (int) $this->getRequest()->getParam('store', 0);
    return Mage::app()->getStore($storeId);
  }

  protected function _prepareCollection()
  {
    $store = $this->_getStore();
    if ($this->getCategory()->getId()) {
      $this->setDefaultFilter(array('in_category'=>1));
    }
    $collection = Mage::getModel('catalog/product')->getCollection()
      ->addAttributeToSelect('name')
      ->addAttributeToSelect('sku')
      ->addAttributeToSelect('price')
      ->addAttributeToSelect('ecommerce')
      ->addStoreFilter($this->getRequest()->getParam('store'))
       ->joinField('qty',
         'cataloginventory/stock_item',
         'qty',
         'product_id=entity_id',
         '{{table}}.stock_id=1',
         'left')
        ->joinField('position',
          'catalog/category_product',
          'position',
          'product_id=entity_id',
          'category_id='.(int) $this->getRequest()->getParam('id', 0),
          'left');
    if ($store->getId() != null) {
      $collection->setStoreId($store->getId());
      $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
      $collection->addStoreFilter($store);
      $collection->joinAttribute(
        'status',
        'catalog_product/status',
        'entity_id',
        null,
        'inner',
        $store->getId()
      );
      $collection->joinAttribute(
        'visibility',
        'catalog_product/visibility',
        'entity_id',
        null,
        'inner',
        $store->getId()
      );
    }
    else {
      $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
      $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
    }

    $this->setCollection($collection);

    if ($this->getCategory()->getProductsReadonly()) {
      $productIds = $this->_getSelectedProducts();
      if (empty($productIds)) {
        $productIds = 0;
      }
      $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
    }

    return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
  }

  protected function _prepareColumns()
  {
    if (!$this->getCategory()->getProductsReadonly()) {
      $this->addColumn('in_category', array(
        'header_css_class' => 'a-center',
        'type'      => 'checkbox',
        'name'      => 'in_category',
        'values'    => $this->_getSelectedProducts(),
        'align'     => 'center',
        'index'     => 'entity_id'
      ));
    }
    $this->addColumn('entity_id', array(
      'header'    => Mage::helper('catalog')->__('ID'),
      'sortable'  => true,
      'width'     => '60',
      'index'     => 'entity_id'
    ));

    $this->addColumn('sku', array(
      'header'    => Mage::helper('catalog')->__('SKU'),
      'width'     => '80',
      'index'     => 'sku'
    ));

    $this->addColumn('name', array(
      'header'    => Mage::helper('catalog')->__('Name'),
      'index'     => 'name'
    ));

    $this->addColumn('price', array(
      'header'    => Mage::helper('catalog')->__('Price'),
      'type'  => 'currency',
      'width'     => '1',
      'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
      'index'     => 'price'
    ));

    $this->addColumn('qty',
      array(
        'header'=> Mage::helper('catalog')->__('Qty'),
        'width' => '100px',
        'type'  => 'number',
        'index' => 'qty',
      ));

    $this->addColumn('visibility',
      array(
        'header'=> Mage::helper('catalog')->__('Visibility'),
        'width' => '70px',
        'index' => 'visibility',
        'type'  => 'options',
        'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
      ));

    $this->addColumnAfter('status',
      array(
        'header'=> Mage::helper('catalog')->__('Status'),
        'width' => '70px',
        'index' => 'status',
        'type'  => 'options',
        'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
      ),'visibility');

    $this->addColumnAfter('ecommerce',
      array(
        'header'=> Mage::helper('catalog')->__('Ecommerce Attrib.'),
        'width' => '50px',
        'index' => 'ecommerce',
        'type'  => 'options',
        'options' => array(
          '1'   =>  Mage::helper('catalog')->__('Yes'),
          '0'    => Mage::helper('catalog')->__('No'),
        ),
      ),'status');
    $this->addColumn('position', array(
      'header'    => Mage::helper('catalog')->__('Position'),
      'width'     => '1',
      'type'      => 'number',
      'index'     => 'position',
      'editable'  => !$this->getCategory()->getProductsReadonly()
      //'renderer'  => 'adminhtml/widget_grid_column_renderer_input'
    ));

    return parent::_prepareColumns();
  }
}
<?php
class Magebuzz_Dealerlocator_Block_Adminhtml_Rewrite_Catalog_Product_Edit_Tabs_Dealer extends Mage_Adminhtml_Block_Widget_Grid{
  public function __construct() {
    parent::__construct();
    $this->getSelectedDealers();
    $this->setId('productdealer');
    $this->setDefaultSort('dealerlocator_id');
    /* $this->setDefaultFilter(array('in_dealer' => 1)); */
    $this->setDefaultDir('ASC');
    $this->setUseAjax(TRUE);
  }
  
  protected function _addColumnFilterToCollection($column) {

    if ($column->getId() == 'in_dealer') {
      $dealers = $this->getSelectedDealerSession();
      if (empty($dealers)) {
        $dealers = 0;
      }
      if ($column->getFilter()->getValue()) {
        $this->getCollection()->addFieldToFilter('dealerlocator_id', array('in' => $dealers));
      } else {
        if ($dealers) {
          $this->getCollection()->addFieldToFilter('dealerlocator_id', array('nin' => $dealers));
        }
      }
    } else {
      parent::_addColumnFilterToCollection($column);
    }
    return $this;
  }
  
  protected function _prepareCollection() {
    $current_store_id = Mage::app()->getRequest()->getParam('store');
    $collection = Mage::getModel('dealerlocator/dealerlocator')->getCollection();
    $this->setCollection($collection);
    return parent::_prepareCollection();
  }
  
  protected function _prepareColumns() {
    $this->addColumn('in_dealer',
      array(
        'header_css_class' => 'a-center',
        'type'      => 'checkbox',
        'name'      => 'in_dealer',
        'align'     => 'center',
        'index'     => 'dealerlocator_id',
        'values'    => $this->getSelectedDealerSession(),
    ));
    
    $this->addColumn('dealerlocator_id',
      array(
        'header' => Mage::helper('dealerlocator')->__('ID'),
        'align' => 'right',
        'width' => '50px',
        'index' => 'dealerlocator_id',
    ));

    $this->addColumn('title',
      array(
        'header' => Mage::helper('dealerlocator')->__('Title'),
        'align' => 'left',
        'index' => 'title',
    ));

    $this->addColumn('address',
      array(
        'header' => Mage::helper('dealerlocator')->__('Address'),
        'align' => 'left',
        'index' => 'address',
    ));

    $this->addColumn('longitude',
      array(
        'header' => Mage::helper('dealerlocator')->__('Longitude'),
        'align' => 'left',
        'index' => 'longitude',
    ));

    $this->addColumn('latitude',
      array(
        'header' => Mage::helper('dealerlocator')->__('Latitude'),
        'align' => 'left',
        'index' => 'latitude',
    ));

    $this->addColumn('status',
      array(
        'header' => Mage::helper('dealerlocator')->__('Status'),
        'align' => 'left',
        'index' => 'status',
        'type' => 'options',
        'options' => array(
          1 => 'Enabled',
          2 => 'Disabled',
        ),
    ));

    return parent::_prepareColumns();
  }
  
  public function getGridUrl() {
    return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/grid', array('_current' => TRUE));
  }
  
  public function getSelectedDealers(){
    //dealers was assign for product in data
    $productId = $this->getRequest()->getParam('id');
    if(!$productId){
      return null;
    }
    $current_store_id = Mage::app()->getRequest()->getParam('store');
    $productdealerCollection = Mage::getModel('dealerlocator/productdealer')
      ->getCollection()
      ->addFieldToFilter('product_id', $productId)
      ->addFieldToFilter('store_id', $current_store_id)
      ->addFieldToFilter('display', 1);
    $dealers = $productdealerCollection->getColumnValues('dealer_id');
    return $dealers;
  }
  
  public function getSelectedDealerSession(){
    $dealers = $this->getDealers();
    if($dealers == null){
      $dealers = $this->getSelectedDealers();
    }
    return $dealers;
  }
}

<?php
class Magebuzz_Dealerlocator_Block_Productdealer extends Mage_Core_Block_Template{
  public function _prepareLayout() {
    parent::_prepareLayout();
    $showPerPageOptions = Mage::helper('dealerlocator')->getShowPerPageOptions();
    if (empty($showPerPageOptions)) {
      $showPerPageOptions = array(10 => 10, 20 => 20, 30 => 30);
    }
    $pager = $this->getLayout()->createBlock('page/html_pager', 'dealer.pager');
    $pager->setAvailableLimit($showPerPageOptions);
    $pager->setLimit(Mage::helper('dealerlocator')->getDefaultShowPerPage());
    $collection = $this->getProductDealer();
    $pager->setCollection($collection);
    $this->setChild('pager', $pager);
    return $this;
  }
  
  public function getPagerHtml(){
    return $this->getChildHtml('pager');
  }
  
  public function getProductDealer(){
    $data = $this->getRequest()->getParams();
    $productId = $data['id'];
    $dealerlocatorTable = Mage::getSingleton('core/resource')->getTableName('dealerlocator');
    
    $productdealerModel = Mage::getModel('dealerlocator/productdealer');
    $productdealerCollection = $productdealerModel->getCollection()
      ->addFieldToFilter('product_id', $productId);
    
    $productdealerCollection->join(array('deatbl' => $dealerlocatorTable), 'deatbl.dealerlocator_id = main_table.dealer_id', array('deatbl.*'));
    return $productdealerCollection;
  }
  
  public function getProductDealDefaultLatLong() {
    $helper = Mage::helper('dealerlocator');
    $defaultLocation = $helper->getDefaultAddress();
    $defaultLatLong = array();

    //set default location by address
    if ($defaultLocation != '') {
      $address = urlencode($defaultLocation);
      $json = $helper->getJsonData($address);
      $defaultLatLong = array('lat' => strval($json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'}), 'long' => strval($json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'}));
    }
    
    //if it is empty, set to a default location to avoid problem
    if (empty($defaultLatLong)) {
      $defaultLatLong = array('lat' => '29.737354', 'long' => '-95.416767');
    }

    return $defaultLatLong;
  }
}
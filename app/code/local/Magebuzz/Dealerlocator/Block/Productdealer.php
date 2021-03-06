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
    $pager->setTemplate('dealerlocator/pager.phtml');
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
    $dealerStoreTable = Mage::getSingleton('core/resource')->getTableName('dealerlocator_store');
    $productDealerTable = Mage::getSingleton('core/resource')->getTableName('productdealer');
    $dealerlocatorTagTable = Mage::getSingleton('core/resource')->getTableName('dealerlocator_tag');
    $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, Mage::app()->getStore()->getId());
    
    // $productdealerModel = Mage::getModel('dealerlocator/productdealer');
    // $productdealerCollection = $productdealerModel->getCollection()
      // ->addFieldToFilter('product_id', $productId)
      // ->addFieldToFilter('main_table.store_id', $current_store_id)
        // ->addFieldToFilter('display', 1);

    // $productdealerCollection->join(array('deatbl' => $dealerlocatorTable), 'deatbl.dealerlocator_id = main_table.dealer_id', array('deatbl.*'))
      // ->addFieldToFilter('deatbl.status', '1');
      
    // $productdealerCollection->join(array('dealerStore' => $dealerStoreTable), 'dealerStore.dealer_id=main_table.dealer_id', array('dealerStore.store_id'))
      // ->addFieldToFilter('dealerStore.store_id', array('in' => $storeIds));
      
    $productdealerCollection = Mage::getModel('dealerlocator/dealerlocator')->getCollection() 
      ->join(array('productDealer' => $productDealerTable), 'productDealer.dealer_id = main_table.dealerlocator_id', array('productDealer.*'))
      ->join(array('dealerStore' => $dealerStoreTable), 'dealerStore.dealer_id=main_table.dealerlocator_id', array('dealerStore.store_id'))
      ->join(array('dealerlocatorTag' => $dealerlocatorTagTable), 'dealerlocatorTag.dealer_id=dealerStore.dealer_id', array('dealerlocatorTag.tag'))
      ->addFieldToFilter('productDealer.product_id', $productId)
      ->addFieldToFilter('productDealer.store_id', array('in' => $storeIds))
      ->addFieldToFilter('dealerStore.store_id', array('in' => $storeIds))
      ->addFieldToFilter('status', 1);
    $productdealerCollection->getSelect()->order('dealerlocatorTag.tag', 'asc');
    $productdealerCollection->getSelect()->group('main_table.dealerlocator_id');
    
    return $productdealerCollection;
  }
  
  public function getProductDealerListTag() {
    $dealerCollection = $this->getProductDealer();
    $dealerIds = $dealerCollection->getColumnValues('dealerlocator_id');
    $tagCollection = Mage::getModel('dealerlocator/tag')->getCollection()->addFieldToFilter('dealer_id', array('in' => $dealerIds));
    $tagCollection->getSelect()->order('tag', 'asc');
    $tags = array();
    if (count($tagCollection)) {
      foreach ($tagCollection as $tag) {
        $_newTag = strtolower(trim($tag->getTag()));
        if (!in_array($_newTag, $tags)) {
          $tags[] = $_newTag;
        }
      }
    }
    return $tags;
  }
  
  public function getProductDealerByTag($tag){
    $assignedDealerIds = $this->getProductDealer()->getColumnValues('dealer_id');
    $tagModel = Mage::getModel('dealerlocator/tag');
    $dealerIds = $tagModel->getCollection()->addFieldToFilter('tag', $tag)
      ->addFieldToFilter('dealer_id', array('in' => $assignedDealerIds))
      ->getColumnValues('dealer_id');
    return $dealerIds;
  }
  
  public function getProductDealDefaultLatLong() {
    $helper = Mage::helper('dealerlocator');
    $defaultLatLong = array();
    $defaultLocation = $this->getProductDealer()->getFirstItem();
    if ($defaultLocation->getLongitude() && $defaultLocation->getLatitude()) {
      $defaultLatLong = array(
        'lat' => $defaultLocation->getLatitude(),
        'long' => $defaultLocation->getLongitude(),       
      );
    }
    else {
      $address = $helper->getDefaultAddress();
      $address = urlencode($address);
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
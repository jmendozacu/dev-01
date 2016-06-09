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
			->addFieldToFilter('productDealer.product_id', $productId)
			->addFieldToFilter('productDealer.store_id', array('in' => $storeIds))
			->addFieldToFilter('dealerStore.store_id', array('in' => $storeIds));
			
    return $productdealerCollection;
  }
	
	public function getProductDealerListTag() {
    $dealerCollection = $this->getProductDealer();
    $dealerIds = $dealerCollection->getColumnValues('dealerlocator_id');
    $tagCollection = Mage::getModel('dealerlocator/tag')->getCollection()->addFieldToFilter('dealer_id', array('in' => $dealerIds));
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
    $tagModel = Mage::getModel('dealerlocator/tag');
    $dealerIds = $tagModel->getCollection()->addFieldToFilter('tag', $tag)->getColumnValues('dealer_id');
    $productdealerCollection = $this->getProductDealer();
    if(count($dealerIds)>0){
      $productdealerCollection->addFieldToFilter('dealerlocator_id', array('in' => $dealerIds));
    }
    return $productdealerCollection;
  }
  
  public function getProductDealDefaultLatLong() {
    $helper = Mage::helper('dealerlocator');
    $defaultLatLong = array();
		$defaultLocation = $this->getProductDealer()->getFirstItem();
		$address = $defaultLocation->getAddress();
		if ($address == '') {
			$address = $helper->getDefaultAddress();
		}
    //set default location by address
    if ($address != '') {
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
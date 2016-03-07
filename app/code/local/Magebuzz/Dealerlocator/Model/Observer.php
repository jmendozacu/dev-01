<?php
class Magebuzz_Dealerlocator_Model_Observer {
  public function setDealerForProduct($observer){
    $data = Mage::app()->getRequest()->getParams();
    
    if(isset($data['in_dealer'])){
      $productdealerModel = Mage::getModel('dealerlocator/productdealer');
      $productId = $data['id'];
      
      $dealerOlds = $productdealerModel->getCollection()
        ->addFieldToFilter('product_id', $productId)
        ->getColumnValues('dealer_id');

      $dealerPosts = array();
      $dealerPosts = explode('&', $data['in_dealer']);
      
      $dealerAdds = array_diff($dealerPosts, $dealerOlds);
      $dealerDels = array_diff($dealerOlds, $dealerPosts);
      
      //delete product dealer
      $productdealerIdDels = $productdealerModel->getCollection()
        ->addFieldToFilter('product_id', $productId)
        ->addFieldToFilter('dealer_id', array('in' => $dealerDels))
        ->getColumnValues('productdealer_id');
      
      foreach($productdealerIdDels as $productdealerIdDel){
        $productdealerModel->setId($productdealerIdDel)->delete();
      }
      
      //add new dealer
      if($dealerAdds != null){
        foreach($dealerAdds as $dealerAdd){
          $dataForSave['product_id'] = $productId;
          $dataForSave['dealer_id'] = $dealerAdd;
          $productdealerModel->setData($dataForSave);
          $productdealerModel->save();
        }
      }
    }
  }
}
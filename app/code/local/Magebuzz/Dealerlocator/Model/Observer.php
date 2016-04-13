<?php
class Magebuzz_Dealerlocator_Model_Observer {
  public function setDealerForProduct($observer){
    $data = Mage::app()->getRequest()->getParams();
    $product = $observer->getProduct();
    if(isset($data['in_dealer'])){
        $productdealerModel = Mage::getModel('dealerlocator/productdealer');
        $productId = $product->getId();

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
            $dataForSave['display'] = 1;
            $productdealerModel->setData($dataForSave);
            $productdealerModel->save();
          }
        }
      }
  }
}
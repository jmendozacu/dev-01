<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Bannerads_IndexController extends Mage_Core_Controller_Front_Action {

  public function disableCategoryAction(){
    $this->getResponse()->setHeader('Content-type','application/json');

    $data = $this->getRequest()->getPost();
    $categoryId = $data['categoryId'];
    $result = array();
    if ($categoryId) {
      Mage::helper('bannerads')->saveStatus($categoryId,0);
      $result['success'] = true;
    }
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }
}
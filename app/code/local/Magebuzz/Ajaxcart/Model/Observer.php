<?php
/*
* Copyright (c) 2016 www.magebuzz.com 
*/

class Magebuzz_Ajaxcart_Model_Observer {
  public function checkCustomOptions($observer) {
    $params = $observer->getControllerAction()->getRequest()->getParams();
    $product = Mage::registry('current_product');

    /* check need display popup */
    if (!isset($params['options']) || $params['options'] != 'cart') {
      return;
    }

    /* if product is bundle go to product page*/
//    if ($product->getTypeId() == 'bundle') {
//      $observer->getControllerAction()->getResponse()->setHeader('Content-type', 'application/json');
//      $_response = array();
//      $_response['success'] = 'true';
//      $_response['redirect_url'] = $product->getUrl();
//      $observer->getControllerAction()->getResponse()->setBody(json_encode($_response));
//      return;
//    }

    $observer->getControllerAction()->getResponse()->setHeader('Content-type', 'application/json');
    $_response = array();

    $html_popup = Mage::helper('ajaxcart')->getOptionsPopupHtml($product);
    $_response['success'] = 'true';
    $_response['html_popup'] = $html_popup;
    $observer->getControllerAction()->getResponse()->setBody(json_encode($_response));
  }
}
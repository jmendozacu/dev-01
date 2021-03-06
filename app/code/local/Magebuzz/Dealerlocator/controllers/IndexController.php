<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Dealerlocator_IndexController extends Mage_Core_Controller_Front_Action {

  public function getSuggestionsAction() {
    $params = $this->getRequest()->getParams();
    if ($params['query']) {
      $address = urlencode($params['query']);
      $storeId = Mage::app()->getStore()->getId();
      $configUrl = Mage::getStoreConfig('dealerlocator/google_map_options/google_geo_api_url', $storeId);
      if ($configUrl != '') {
        $url = Mage::getStoreConfig('dealerlocator/google_map_options/google_geo_api_url') . "?address=$address&sensor=false";
      } else {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&sensor=false';
      }

      $rCURL = curl_init();
      curl_setopt($rCURL, CURLOPT_URL, $url);
      curl_setopt($rCURL, CURLOPT_HEADER, 0);
      curl_setopt($rCURL, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
      curl_setopt($rCURL, CURLOPT_RETURNTRANSFER, 1);
      $jsonData = curl_exec($rCURL);
      $data = json_decode($jsonData, TRUE);
      $results = "<ul>\n";
      foreach ($data['results'] as $item) {
        $url = Mage::getUrl('*', array('q' => urlencode($item['formatted_address']), 'd' => $params['d']));
        $results .= '<li url="' . $url . '">';
        $results .= $item['formatted_address'];
        $results .= "</li>\n";
      }
      $results .= "</ul>\n";
      curl_close($rCURL);
      $this->getResponse()->setHeader('Content-type', 'text/html');
      $this->getResponse()->setBody($results);
    }
  }

  public function indexAction() {
    $this->loadLayout();
    $head = $this->getLayout()->getBlock('head');
    $head->setTitle($this->__('Store Locator'));
    $google_key = Mage::getStoreConfig('dealerlocator/google_map_options/google_api_key');
    $googleJs = 'https://maps.googleapis.com/maps/api/js?key=' . $google_key . '&sensor=true&v=3.8';
    $head->addItem('external_js', $googleJs);
    $this->renderLayout();
  }

  public function testAction() {
    echo "123";
    echo Mage::getStoreConfig('dealerlocator/google_map_options/default_dealer_icon');
  }

}
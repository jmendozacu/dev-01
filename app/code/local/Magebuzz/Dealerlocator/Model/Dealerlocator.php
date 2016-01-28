<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Dealerlocator_Model_Dealerlocator extends Mage_Core_Model_Abstract {

  public function _construct() {
    parent::_construct();
    $this->_init('dealerlocator/dealerlocator');
  }

  public function getDealerIconImage() {
    $imageUrl = '';
    $icon_image = $this->getIconImage();
    //$mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'dealers/icons/';
    if ($icon_image != '' && $icon_image != NULL) {
      return $icon_image;
    } else {
      return Mage::helper('dealerlocator')->getDefaultDealerIcon();
    }
  }

}
<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Delivery_Block_Delivery extends Mage_Core_Block_Template {
  protected function _construct() {
    parent::_construct();
      $this->setTemplate('delivery/delivery.phtml');
  }


  public function _prepareLayout() {
    parent::_prepareLayout();
    return $this;
  }

  public function getCountryHtmlSelect($defValue=null, $name='country_id', $id='country', $title='Country')
  {
    if (is_null($defValue)) {
      $defValue = $this->getCountryId();
    }
    $cacheKey = 'DIRECTORY_COUNTRY_SELECT_STORE_'.Mage::app()->getStore()->getCode();
    if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
      $options = unserialize($cache);
    } else {
      $options = $this->getCountryCollection()->toOptionArray();
      if (Mage::app()->useCache('config')) {
        Mage::app()->saveCache(serialize($options), $cacheKey, array('config'));
      }
    }
    $html = $this->getLayout()->createBlock('core/html_select')
      ->setName($name)
      ->setId($id)
      ->setTitle(Mage::helper('directory')->__($title))
      ->setClass('validate-select')
      ->setValue($defValue)
      ->setOptions($options)
      ->getHtml();

    return $html;
  }

  public function getCountryId()
  {
      $countryId = Mage::helper('core')->getDefaultCountry();
    return $countryId;
  }
  public function getCountryCollection()
  {
    $collection = $this->getData('country_collection');
    if (is_null($collection)) {
      $collection = Mage::getModel('directory/country')->getResourceCollection()
        ->loadByStore();
      $this->setData('country_collection', $collection);
    }

    return $collection;
  }

  public function getActionOfForm(){
    return Mage::getUrl('delivery/result/index');
  }





}
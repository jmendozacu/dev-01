<?php

class Magebuzz_Bannerads_Model_Resource_Catalog_Category extends Mage_Catalog_Model_Resource_Category {

  protected function _afterSave(Varien_Object $object)
  {
    /**
     * Add identifier for new category
     */
    if (substr($object->getPath(), -1) == '/') {
      $object->setPath($object->getPath() . $object->getId());
      $this->_savePath($object);
    }
    $this->_saveCategoryProducts($object);
    $this->_checkTimeToActive($object);
    return parent::_afterSave($object);
  }


  protected function _checkTimeToActive($category)
  {
    $now_time = date(Varien_Date::DATETIME_PHP_FORMAT, Mage::getModel('core/date')->timestamp(time()));
    $is_active = $category->getData('is_active');
    $from_date = $category->getData('display_from_date');
    $to_date = $category->getData('display_to_date');
    if(!$from_date && !$to_date){
      $is_active = $category->getData('is_active');
    }
    if($from_date && $to_date){
      $storeTimeStamp = strtotime($now_time);
      $fromTimeStamp  = strtotime($from_date);
      $toTimeStamp    = strtotime($to_date);
      if(!($fromTimeStamp <= $storeTimeStamp && $storeTimeStamp <= $toTimeStamp)){
        $is_active = 0;
      }else{
        $is_active = 1;
      }
    }
    if(!$from_date && $to_date){
      $storeTimeStamp = strtotime($now_time);
      $toTimeStamp    = strtotime($to_date);
      if(!($storeTimeStamp <= $toTimeStamp)){
        $is_active = 0;
      }else{
        $is_active = 1;
      }
    }
    if($from_date && !$to_date){
      $storeTimeStamp = strtotime($now_time);
      $fromTimeStamp  = strtotime($from_date);
      if(!($fromTimeStamp <= $storeTimeStamp )){
        $is_active = 0;
      }else{
        $is_active = 1;
      }
    }
    if($is_active!== false){
       Mage::helper('bannerads')->saveStatus($category->getData('entity_id'),$is_active);
    }
  }
}
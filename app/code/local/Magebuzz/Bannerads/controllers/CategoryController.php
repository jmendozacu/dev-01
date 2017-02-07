<?php

require_once 'Mage/Catalog/controllers/CategoryController.php';


class Magebuzz_Bannerads_CategoryController extends Mage_Catalog_CategoryController {

  protected function _initCatagory()
  {
    Mage::dispatchEvent('catalog_controller_category_init_before', array('controller_action' => $this));
    $categoryId = (int) $this->getRequest()->getParam('id', false);
    if (!$categoryId) {
      return false;
    }

    $category = Mage::getModel('catalog/category')
      ->setStoreId(Mage::app()->getStore()->getId())
      ->load($categoryId);

    if(!$this->checkDate($category)){
      return false;
    }
    if (!Mage::helper('catalog/category')->canShow($category)) {
      return false;
    }
    Mage::getSingleton('catalog/session')->setLastVisitedCategoryId($category->getId());
    Mage::register('current_category', $category);
    Mage::register('current_entity_key', $category->getPath());

    try {
      Mage::dispatchEvent(
        'catalog_controller_category_init_after',
        array(
          'category' => $category,
          'controller_action' => $this
        )
      );
    } catch (Mage_Core_Exception $e) {
      Mage::logException($e);
      return false;
    }

    return $category;
  }

  protected function checkDate($modelCategory){
    $now_time = date(Varien_Date::DATETIME_PHP_FORMAT, Mage::getModel('core/date')->timestamp(time()));
    $from_date = $modelCategory->getData('display_from_date');
    $to_date = $modelCategory->getData('display_to_date');
    if(!$from_date && !$to_date){
      return true;
    }
    if($from_date && $to_date){
      $storeTimeStamp = strtotime($now_time);
      $fromTimeStamp  = strtotime($from_date);
      $toTimeStamp    = strtotime($to_date);
      if(!($fromTimeStamp <= $storeTimeStamp && $storeTimeStamp <= $toTimeStamp)){
        return false;
      }else{
        return true;
      }
    }
    if(!$from_date && $to_date){
      $storeTimeStamp = strtotime($now_time);
      $toTimeStamp    = strtotime($to_date);
      if(!($storeTimeStamp <= $toTimeStamp)){
        return false;
      }else{
        return true;
      }
    }
    if($from_date && !$to_date){
      $storeTimeStamp = strtotime($now_time);
      $fromTimeStamp  = strtotime($from_date);
      if(!($fromTimeStamp <= $storeTimeStamp )){
        return false;
      }else{
        return true;
      }
    }
  }
}
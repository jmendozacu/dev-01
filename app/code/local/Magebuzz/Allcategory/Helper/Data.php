<?php

class Magebuzz_Allcategory_Helper_Data extends Mage_Core_Helper_Abstract{
  public function getCatChildByGroup($catGroupId){
    $cat_child = array();
    $categories_child = Mage::getModel('catalog/category')->getCategories($catGroupId);
    foreach($categories_child as $category){
      $cat = Mage::getModel('catalog/category')->load($category->getId());
      $cat_child[] = $cat->getData();
    }
    return $cat_child;
  }
}
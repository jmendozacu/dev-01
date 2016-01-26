<?php
class Magebuzz_Allcategory_Block_Page extends Mage_Catalog_Block_Product_Abstract{
  public function getCatLevel2(){
    $cat_level_2 = array();
    $rootcatIdByStore = Mage::app()->getStore()->getRootCategoryId();
    $categories_level_2 = Mage::getModel('catalog/category')->getCategories($rootcatIdByStore);
    foreach($categories_level_2 as $category){
      $cat = Mage::getModel('catalog/category')->load($category->getId());
      $cat_level_2[] = $cat->getData();
    }
    return $cat_level_2;
  }
  
  public function getCatLevel3($cat2Id){
    $cat_level_3 = array();
    $categories_level_3 = Mage::getModel('catalog/category')->getCategories($cat2Id);
    foreach($categories_level_3 as $category){
      $cat = Mage::getModel('catalog/category')->load($category->getId());
      $cat_level_3[] = $cat->getData();
    }
    return $cat_level_3;
  }
  
  public function getCatLevel4($cat3Id){
    $cat_level_4 = array();
    $categories_level_4 = Mage::getModel('catalog/category')->getCategories($cat3Id);
    foreach($categories_level_4 as $category){
      $cat = Mage::getModel('catalog/category')->load($category->getId());
      $cat_level_4[] = $cat->getData();
    }
    return $cat_level_4;
  }
}
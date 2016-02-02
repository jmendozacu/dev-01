<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Homepage_Block_Discount extends Mage_Catalog_Block_Product_Abstract {

  protected function _getProductCollectionWithMostDiscount($category_id, $min_discount) {
    $category = Mage::getModel('catalog/category')->load($category_id);
    $collection = Mage::getResourceModel('catalog/product_collection')
      ->setStoreId(Mage::app()->getStore()->getId())
      ->addCategoryFilter($category)
      ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
      ->addAttributeToFilter('visibility',  array(
        Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
        Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
      ))
      ->addMinimalPrice()
      ->addFinalPrice()
      ->addTaxPercents()
      ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
      ->addUrlRewrite();
    ;
    $collection->getSelect()->having("((`price` - `minimal_price`) * 100 / `price`) >= $min_discount");
    return $collection;
  }

  protected function _getTopProductCollection() {
    $category_id = $this->getFirstCategoryId();
    $min_discount = $this->getFirstMinDiscount();
    return $this->_getProductCollectionWithMostDiscount($category_id, $min_discount);
  }

  protected function _getFirstBottomProductCollection() {
    $category_id = $this->getSecondCategoryId();
    $min_discount = $this->getSecondMinDiscount();
    return $this->_getProductCollectionWithMostDiscount($category_id, $min_discount);
  }

  protected function _getSecondBottomProductCollection() {
    $category_id = $this->getThirdCategoryId();
    $min_discount = $this->getThirdMinDiscount();
    return $this->_getProductCollectionWithMostDiscount($category_id, $min_discount);
  }

  protected function _getHighlightProduct() {
    $product_id = $this->getHighlightProductId();
    return Mage::getModel('catalog/product')->load($product_id);
  }
}
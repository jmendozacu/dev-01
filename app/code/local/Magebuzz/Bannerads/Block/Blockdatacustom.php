<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Bannerads_Block_Blockdatacustom extends Mage_Core_Block_Template {
  public function _prepareLayout() {
    return parent::_prepareLayout();
  }

  public function getBannerads() {
    $blockId = $this->getBlockBannerId();
    $imageModel = Mage::getModel('bannerads/images');
    $blockModel = Mage::getResourceModel('bannerads/bannerads');
    $blockData = Mage::getModel('bannerads/bannerads')->load($blockId);

    $blockImage = $blockModel->lookupImagesId($blockId);
    if ($blockData->getDisplayType() == 2) {
      $images = $imageModel->load($blockImage[array_rand($blockImage, 1)]);
    } else {
      $images = $imageModel->getCollection()->addFieldToFilter('banner_id', array('in' => $blockImage))->addFieldToFilter('status', 1)->setOrder('sort_order', "ASC");
    }
    $blockData->setImages($images);

    return $blockData;
  }

}

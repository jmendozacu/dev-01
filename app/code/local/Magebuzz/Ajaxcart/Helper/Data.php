<?php
/*
* Copyright (c) 2016 www.magebuzz.com 
*/
class Magebuzz_Ajaxcart_Helper_Data extends Mage_Core_Helper_Abstract {
  public function getMiniCartHtml() {
    $layout = Mage::getModel('core/layout');
    $layout->getUpdate()->load('ajaxcart_minicart_html');
    $layout->generateXml();
    $layout->generateBlocks();
    return $layout->getOutput();
  }

  public function getSidebarCartHtml() {
    $layout = Mage::getModel('core/layout');
    $layout->getUpdate()->load('ajaxcart_sidebarcart_html');
    $layout->generateXml();
    $layout->generateBlocks();
    return $layout->getOutput();
  }

  public function getOptionsPopupHtml($product) {
    $layout = Mage::getModel('core/layout');
    $update = $layout->getUpdate();
    $update->load('ajaxcart_optionspopup_html');

    if ($product->isConfigurable()) {
      $update->addHandle('ajaxcart_PRODUCT_TYPE_configurable');
    }

    if ($product->getTypeId() == 'grouped') {
      $update->addHandle('ajaxcart_PRODUCT_TYPE_grouped');
    }

    if ($product->getTypeId() == 'bundle') {
      $update->addHandle('ajaxcart_PRODUCT_TYPE_bundle');
    }

    if ($product->getTypeId() == 'downloadable') {
      $update->addHandle('ajaxcart_PRODUCT_TYPE_downloadable');
    }

    $update->load();
    $layout->generateXml();
    $layout->generateBlocks();
    return $layout->getOutput();
  }

  public function getSuccessHtml($product) {
    $block = Mage::getSingleton('core/layout')->createBlock('core/template', 'ajax_succeed_message');
    $block->setTemplate('ajaxcart/popup_notification.phtml');
    $block->setProduct($product);
    return $block->renderView();
  }
}
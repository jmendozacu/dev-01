<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/

class Magebuzz_Bannerads_Model_Observer {

  public function loadTimeleftCategory(Varien_Event_Observer $observer){
    if ($observer->getAction()
      && in_array($observer->getAction()->getFullActionName(), array('catalog_category_view'))
    ){
      $category = Mage::registry('current_category');
      if($category->getData('display_to_date')){
        $observer->getAction()->getLayout()->getUpdate()->addHandle('_category_countdown_timeleft');
      }
    }
  }

  public function changeStatus(){
    $categoryCollection = Mage::getModel('catalog/category')->getCollection()
      ->addAttributeToSelect('is_active')
      ->addAttributeToSelect('display_to_date')
      ->addAttributeToSelect('display_from_date');

    $now_time = date(Varien_Date::DATETIME_PHP_FORMAT, Mage::getModel('core/date')->timestamp(time()));
    foreach ($categoryCollection as $category) {
      $from_date = $category->getData('display_from_date');
      $to_date = $category->getData('display_to_date');
      $is_active = $category->getData('is_active');

      if (!$from_date && !$to_date) {
        continue;
      }
      if ($from_date && $to_date) {
        $storeTimeStamp = strtotime($now_time);
        $fromTimeStamp = strtotime($from_date);
        $toTimeStamp = strtotime($to_date);
        if (!($fromTimeStamp <= $storeTimeStamp && $storeTimeStamp <= $toTimeStamp)) {
          $is_active = 0;
        } else {
          $is_active = 1;
        }
      }
      if (!$from_date && $to_date) {
        $storeTimeStamp = strtotime($now_time);
        $toTimeStamp = strtotime($to_date);
        if (!($storeTimeStamp <= $toTimeStamp)) {
          $is_active = 0;
        } else {
          $is_active = 1;
        }
      }
      if ($from_date && !$to_date) {
        $storeTimeStamp = strtotime($now_time);
        $fromTimeStamp = strtotime($from_date);
        if (!($fromTimeStamp <= $storeTimeStamp)) {
          $is_active = 0;
        } else {
          $is_active = 1;
        }
      }

      if ((int)$category->getData('is_active') != $is_active) {
        if ($is_active !== false) {
          Mage::helper('bannerads')->saveStatus($category->getData('entity_id'),$is_active);
        }
      }
    }
  }

  public function checkBlockLoad($observer) {
    $blockModel = Mage::getModel('bannerads/bannerads')->getCollection()->addFieldToFilter('status', 1);
    $storeId = Mage::app()->getStore()->getStoreId();
    $myXml = '';
    foreach ($blockModel as $key => $block) {
      $model = Mage::getModel('bannerads/bannerads')->load($block->getBlockId());
      $store = $model->getStoreId();
      if (in_array(0, $store)) {
        $store[] = $storeId;
      }
      if (in_array($storeId, $store)) {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer->getId()) {
          $customerGroupId = 0;
        } else {
          $customerGroupId = $customer->getGroupId();
        }
        $customerGroups = unserialize($block->getCustomerGroupIds());
        if (in_array($customerGroupId, $customerGroups)) {
          $positionData = $this->getPosition();
          $position = $positionData[$block->getBlockPosition];
          $myXml .= $this->renderXml($position[0], $position[1], $block->getBlockPosition());
        }
      }
    }

    $layout = $observer->getEvent()->getLayout();
    $layout->getUpdate()->addUpdate($myXml);
    $layout->generateXml();

  }

  public function getPosition() {
    $before = 'before="-"';
    $after = 'after="-"';
    $arrayPosition = array('sidebar-right-top' => array('right', $before), 'sidebar-right-bottom' => array('right', $after), 'sidebar-left-top' => array('left', $before), 'sidebar-left-bottom' => array('left', $after), 'content-top' => array('content', $before), 'content-bottom' => array('content', $after), 'menu-top' => array('menu.top', $before), 'menu-bottom' => array('menu.top', $after), 'page-bottom' => array('before_body_end', $before), 'customer-content-top' => array('content', $before), 'customer-content-bottom' => array('content', $after), 'cart-content-top' => array('content', $before), 'cart-content-bottom' => array('content', $after), 'checkout-content-top' => array('content', $before), 'checkout-content-bottom' => array('content', $after), 'checkout-right-top' => array('right', $before), 'checkout-right-bottom' => array('right', $after),);
    return $arrayPosition;
  }

  public function renderXml($position, $subPosition, $name) {
    $textXml = '<reference name="' . $position . '">';
    $textXml .= '<block type="bannerads/bannerads" name="' . $name . '" template="bannerads/bannerads.phtml" ' . $subPosition . '>';
    $textXml .= '</block>';
    $textXml .= '</reference>';
    return $textXml;
  }


}

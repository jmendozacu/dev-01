<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Delivery_IndexController extends Mage_Core_Controller_Front_Action
{

  public function indexAction()
  {
    $this->loadLayout();
    $head = $this->getLayout()->getBlock('head');
    $head->setTitle($this->__('Delivery & Shipment'));
    $this->renderLayout();
  }

  public function resultAction()
  {
    $costs = $this->getShippingCost();
    $result['cost'] = array();
    if ($costs) {
      $result['success'] = true;
      foreach($costs as $cost){
        $result['cost'][] =  Mage::helper('core')->currency($cost, true, false); ;
      }
    } else {
      $result['success'] = false;
      $result['cost'] = null;
    }
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

  public function getShippingCost()
  {

    $post = $this->getRequest()->getPost();

    $resource = Mage::getSingleton('core/resource');

    $writeConnection = $resource->getConnection('core_write');

    $readConnection = $resource->getConnection('core_read');

    $query = 'SELECT cost_base,method_id FROM ' . $resource->getTableName('amtable/rate') . ' WHERE';
    $query .= ' (shipping_type = "' . $post['shipping_type'] . '" OR ' . 'shipping_type = "0")' . ' AND';
    $query .= ' price_from <= "' . $post['budget'] . '" AND';
    $query .= ' price_to >= "' . $post['budget'] . '" AND';
    $query .= ' (country = "' . $post['country_id'] . '" OR ' . 'country = "0")' . ' AND';
    $query .= ' (state = "' . $post['region_id'] . '" OR ' . 'state = "0")' . ' AND';
    $query .= ' (city = "' . $post['city_id'] . '" OR ' . 'city ="")';

    $results = $readConnection->fetchAll($query);
    /* get the results */
    $cost = array();
    foreach ($results as $key => $result) {
      $method_id = $result['method_id'];
      $method = Mage::getModel('amtable/method')->load($method_id);
      if ($result['cost_base'] < $method->getMinRate()) {
        $result['cost_base'] = $method->getMinRate();
        $cost[$key] = $result['cost_base'];
      }
      if ($result['cost_base'] > $method->getMaxRate()) {
        $result['cost_base'] = $method->getMaxRate();
        $cost[$key] = $result['cost_base'];
      } else {
        $cost[$key] = $result['cost_base'];
      }
    }
    return $cost;
  }
}
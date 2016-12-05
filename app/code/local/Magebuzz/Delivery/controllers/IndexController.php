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

    $readConnection = $resource->getConnection('core_read');

    $query  = 'SELECT rate_id,cost_base,rate.method_id,method.is_active FROM ' . $resource->getTableName('amtable/rate') . ' AS `rate`';
    $query .= ' LEFT JOIN ' . $resource->getTableName('amtable/method'). ' AS `method` ON ' . 'rate.method_id = method.method_id';
    $query .= ' WHERE method.is_active = 1 AND';
    $query .= ' (shipping_type = "' . $post['shipping_type'] . '" OR ' . 'shipping_type = "0")' . ' AND';
    $query .= ' price_from <= "' . $post['budget'] . '" AND';
    $query .= ' price_to >= "' . $post['budget'] . '" AND';
    $query .= ' (country = "' . $post['country_id'] . '" OR ' . 'country = "0")' . ' AND';
    $query .= ' (state = "' . $post['region_id'] . '" OR ' . 'state = "0")' . ' AND';
    $query .= ' (city = "' . $post['city_id'] . '" OR ' . 'city = "" OR city is null)';

    if($post['weight'] && $post['weight']!= null){
      $query .= ' AND weight_from <= "' . $post['weight'] . '" AND';
      $query .= ' weight_to >= "' . $post['weight'] . '"' ;
    }

    $results = $readConnection->fetchAssoc($query);
    /* get the results */
    $cost = array();

    $minRates = Mage::getModel('amtable/method')->getCollection()->hashMinRate();
    $maxRates = Mage::getModel('amtable/method')->getCollection()->hashMaxRate();

    foreach ($results as $key  => $rate){
      $cost[$key] =  $rate['cost_base'];
      if ($maxRates[$rate['method_id']] != '0.00' && $maxRates[$rate['method_id']] < $rate['cost_base']){
        $cost[$key] = $maxRates[$rate['method_id']];
      }

      if ($minRates[$rate['method_id']] != '0.00' && $minRates[$rate['method_id']] > $rate['cost_base']){
        $cost[$key] = $minRates[$rate['method_id']];
      }
    }
    return $cost;
  }
}
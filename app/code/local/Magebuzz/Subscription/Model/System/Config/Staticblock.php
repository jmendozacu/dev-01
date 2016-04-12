<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Subscription_Model_System_Config_Staticblock
{
  public function toOptionArray()
  {
    $staticblocks = Mage::getModel('cms/block')->getCollection();
    $options = array();
    foreach ($staticblocks as $_block) {
      $options[] = array(
        'value' => $_block->getId(),
        'label' => $_block->getTitle(),
      );
    }
    return $options;
  }
}
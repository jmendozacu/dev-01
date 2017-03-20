<?php
class Magebuzz_Career_Helper_Data extends Mage_Core_Helper_Abstract{
  public function getJob(){
    $collection = Mage::getModel('career/job')->getCollection();
    $collection->addFieldToFilter('status','1');
    return $collection;
  }
}
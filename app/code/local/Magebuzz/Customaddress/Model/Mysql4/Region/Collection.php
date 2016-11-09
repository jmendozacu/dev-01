<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Customaddress_Model_Mysql4_Region_Collection extends Magebuzz_Customaddress_Model_Resource_Region_Collection {


  public function _construct() {
    parent::_construct();
    $this->_init('customaddress/region');
  }

  public function _toOptionHashWithThai($valueField='region_id', $labelField='code')
  {
    $res = array();
    foreach ($this as $item) {
      if($item->getData('country_id')=='TH'){
        $res[$item->getData($valueField)] = $item->getData($labelField);
      }
    }
    asort($res);
    return $res;
  }
}
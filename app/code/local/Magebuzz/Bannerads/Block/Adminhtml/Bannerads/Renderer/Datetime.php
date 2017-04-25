<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Bannerads_Block_Adminhtml_Bannerads_Renderer_Datetime extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime {
  public function render(Varien_Object $row)
  {
    if ($data = $this->_getValue($row)) {
      $format = $this->_getFormat();
      try {
        $data = Mage::app()->getLocale()
          ->date($data, Varien_Date::DATETIME_INTERNAL_FORMAT,null,false)->toString($format);
      }
      catch (Exception $e)
      {
        $data = Mage::app()->getLocale()
          ->date($data, Varien_Date::DATETIME_INTERNAL_FORMAT,null,false)->toString($format);
      }
      return $data;
    }
    return $this->getColumn()->getDefault();
  }

}
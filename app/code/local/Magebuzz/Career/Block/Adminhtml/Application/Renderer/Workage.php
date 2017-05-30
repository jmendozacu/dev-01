<?php

class Magebuzz_Career_Block_Adminhtml_Application_Renderer_Workage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
  public function render(Varien_Object $row) {
    $getData = $row->getData();
    $work_age = $getData['work_age'];
    $work_age_title = Mage::helper('career')->getTitleAge($work_age);
    return $work_age_title ;
  }

}
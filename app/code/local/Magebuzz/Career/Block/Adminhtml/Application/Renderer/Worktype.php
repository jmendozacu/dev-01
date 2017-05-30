<?php

class Magebuzz_Career_Block_Adminhtml_Application_Renderer_Worktype extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
  public function render(Varien_Object $row) {
    $getData = $row->getData();
    $work_type = $getData['work_type'];
    $work_type_title = Mage::getModel('career/worktype')->load($work_type)->getData('title');
    return $work_type_title ;
  }

}
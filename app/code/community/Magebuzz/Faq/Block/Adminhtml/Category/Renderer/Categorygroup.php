<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Faq_Block_Adminhtml_Category_Renderer_Categorygroup extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
  public function render(Varien_Object $row) {
    $category = $row->getData();
    $category_group = $category['category_group'];
    if($category_group == Magebuzz_Faq_Model_Categorygroup::ONLINE_FAQ){
      return $this->__('Online FAQ');
    }
    elseif($category_group == Magebuzz_Faq_Model_Categorygroup::IN_STORE_FAQ){
      return $this->__('In-store FAQ');
    }
    elseif($category_group == Magebuzz_Faq_Model_Categorygroup::ABOUT_INDEX){
      return $this->__('About Index Living Mall');
    }
  }

}
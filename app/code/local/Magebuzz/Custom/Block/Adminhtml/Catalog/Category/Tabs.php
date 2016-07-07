<?php

class Magebuzz_Custom_Block_Adminhtml_Catalog_Category_Tabs extends Mage_Adminhtml_Block_Catalog_Category_Tabs {
  protected function _prepareLayout()
  {
    $categoryAttributes = $this->getCategory()->getAttributes();

    foreach ($categoryAttributes as $attribute) {
      if ($attribute->getAttributeCode() == 'image') {
        $attribute->setData('frontend_label','Image 384*346');
      }
      if ($attribute->getAttributeCode() == 'category_thumbnail') {
        $attribute->setData('frontend_label','Image 553*355');
      }
    }

    return parent::_prepareLayout();
  }
}
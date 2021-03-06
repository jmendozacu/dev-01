<?php
class Magebuzz_Allcategory_Model_Observer {
  public function categoryViewLoadBefore(Varien_Event_Observer $observer){
		/** @var $category */
    $category = Mage::registry('current_category');
		if($category instanceof Mage_Catalog_Model_Category ){
			$catModel = Mage::getModel("catalog/category")->load($category->getId());
			//if ($category instanceof Mage_Catalog_Model_Category && $category->getLevel() == '3' && !Mage::registry('current_product')) {
			if ($catModel->getData('promotion_template')) {
				/** @var $layout Mage_Core_Model_Layout */
				$update = Mage::getSingleton('core/layout')->getUpdate();
				$params = Mage::app()->getRequest()->getParams();
				if ($category->hasChildren() && !isset($params['page'])) {
					$update->addHandle('catalog_category_layered_haschildren');
				}
			}
		}
  }
}
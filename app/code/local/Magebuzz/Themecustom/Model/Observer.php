<?php

class Magebuzz_Themecustom_Model_Observer {
	public function isSalableAfter($observer) {
		$product = $observer->getProduct();
		$objectSalable = $observer->getSalable();
		$storeId = Mage::app()->getStore()->getId();
		$isEcommerce = Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), 'ecommerce', $storeId);
		
		if ($isEcommerce) {
			//$objectSalable->setIsSalable(true);
		} else {			
			$objectSalable->setIsSalable(false);
		}
	}
}
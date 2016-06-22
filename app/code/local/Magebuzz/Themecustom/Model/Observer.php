<?php

class Magebuzz_Themecustom_Model_Observer {
	public function isSalableAfter($observer) {
		$product = $observer->getProduct();
		$objectSalable = $observer->getSalable();
		
		if($product->getEcommerce()){
			$objectSalable->setIsSalable(true);
		}else{
			$objectSalable->setIsSalable(false);
		}
	}
}
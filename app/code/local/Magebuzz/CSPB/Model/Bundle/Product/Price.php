<?php
class Magebuzz_CSPB_Model_Bundle_Product_Price extends Mage_Bundle_Model_Product_Price {
	public static function calculateSpecialPrice($finalPrice, $specialPrice, $specialPriceFrom, $specialPriceTo,
                                                 $store = null) {																									
		if (!is_null($specialPrice) && $specialPrice != false) {
			if (Mage::app()->getLocale()->isStoreDateInInterval($store, $specialPriceFrom, $specialPriceTo)) {
				$finalPrice = min($finalPrice, $specialPrice);
			}
		}
		return $finalPrice;
  }
}
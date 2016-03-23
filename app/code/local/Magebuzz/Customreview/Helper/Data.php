<?php
class Magebuzz_Customreview_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getAllImages(){
		$collection = Mage::getModel('review/review')->getCollection();

		return $collection;
	}
}
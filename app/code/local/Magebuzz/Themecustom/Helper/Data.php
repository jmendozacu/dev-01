<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Themecustom_Helper_Data extends Mage_Core_Helper_Abstract {
	protected $_validFeatureAttributes = array('feature_special_1',
	'feature_special_2',
	'feature_special_3',
	'feature_special_4',
	'feature_special_5',
	'feature_special_6',
	'feature_special_7',
	'feature_special_8',
	'feature_special_9',
	'feature_special_10',
	'feature_special_11');
	
	public function isValidFeatureAttribute($product){
		foreach($this->_validFeatureAttributes as $_attributeCode){
			if($this->checkProductAttributeYesNo($product, $_attributeCode)){
				return true;
			}
		};
		return false;
	}
	
	public function checkProductAttributeYesNo($_product, $attribute_code){
		if($_product->getResource()->getAttribute($attribute_code) 
			&& $_product->getResource()->getAttribute($attribute_code)->getFrontend()->getValue($_product) == 'Yes'){
			return true;
		}
		return false;
  }
	
	public function getProductAttributeLabel($_product, $attribute_code){
		if($_product->getResource()->getAttribute($attribute_code)){
			return $_product->getResource()->getAttribute($attribute_code)->getFrontend()->getLabel($_product);
		}
		return '';
	}
}
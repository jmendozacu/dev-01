<?php

/*
* @copyright   Copyright ( c ) 2016 www.magebuzz.com
*/

class Magebuzz_Subscription_Helper_Data extends Mage_Core_Helper_Abstract {
	const XML_PATH_ENABLED_CUSTOM_MSG = 'subscription/general/enable_custom_msg';
	const XML_PATH_ENABLED_STATIC_BLOCK_CUSTOM_MSG = 'subscription/general/use_static_block';
	const XML_PATH_STATICBLOCK_ID = 'subscription/general/popup_static_block';
	
	public function isEnabledCustomMsg() {
    $storeId = Mage::app()->getStore()->getId();
    return (int)Mage::getStoreConfig(self::XML_PATH_ENABLED_CUSTOM_MSG, $storeId);
  }
	
	public function isUseStaticBlockForCustomMsg() {
    $storeId = Mage::app()->getStore()->getId();
    return (int)Mage::getStoreConfig(self::XML_PATH_ENABLED_STATIC_BLOCK_CUSTOM_MSG, $storeId);
  }
	
	public function getPopupStaticBlockId() {
    $storeId = Mage::app()->getStore()->getId();
    return (int)Mage::getStoreConfig(self::XML_PATH_STATICBLOCK_ID, $storeId);
  }
	
	public function getPopupStaticBlockContent() {
		$contentHtml = '';
		$staticBlockId = $this->getPopupStaticBlockId();
		if($staticBlockId) {
			$contentHtml .= Mage::app()->getLayout()->createBlock('cms/block')->setBlockId($staticBlockId)->toHtml();
		}
		return $contentHtml;
	}
}
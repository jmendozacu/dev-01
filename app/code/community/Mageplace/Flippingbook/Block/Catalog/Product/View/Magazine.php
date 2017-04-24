<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Catalog_Product_View_Magazine extends Mage_Catalog_Block_Product_View_Abstract
{
	public function getMagazines()
	{
		$collection = Mage::getResourceModel('flippingbook/magazine_collection');
		$collection->setProductId($this->getProduct()->getId())
			->addProductAttachedFilter(1)
			->addIsActiveFilter()
			->setOrderByPosition()
			->addStoreFilter(Mage::app()->getSafeStore())
			->getItems();

		return $collection;
	}

	public function getPopupWidth($magazine)
	{
		return $magazine->getResolutionWidth()*($magazine->getMagazineViewMode() + 1) + 150 >= 700 ? $magazine->getResolutionWidth()*($magazine->getMagazineViewMode() + 1) + 120 : 700;
	}

	public function getPopupHeight($magazine)
	{
		return $magazine->getResolutionHeight() + 200;
	}

	public function getMagazineUrl($magazine)
	{
		return Mage::helper('flippingbook')->getMagazineUrl($magazine);
	}
}

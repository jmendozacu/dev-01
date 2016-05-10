<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Customwishlist_Helper_Data extends Mage_Core_Helper_Abstract {
	public function checkItemInWishlist($productId) {
		$is_in_wishlist = false;
		if(!Mage::getSingleton('customer/session')->isLoggedIn()) {
			$is_in_wishlist = false;
		}else{
			$customerId = Mage::getSingleton('customer/session')->getCustomerId();
			$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);
			$collection = Mage::getModel('wishlist/item')->getCollection()
					->addFieldToFilter('wishlist_id', $wishlist->getId())
					->addFieldToFilter('product_id', $productId);			
			$is_in_wishlist = (bool) $collection->count();
			if ($is_in_wishlist) {
				$is_in_wishlist = $collection->getFirstItem()->getId();
			}
		}
		return $is_in_wishlist;
	}
	
	public function getWishlistItemIdFromProductId($productId) {
		$wishlistItemId = null;
		if(!Mage::getSingleton('customer/session')->isLoggedIn()) {
			return;
		}else{
			$customerId = Mage::getSingleton('customer/session')->getCustomerId();
			$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);
			$collection = Mage::getModel('wishlist/item')->getCollection()
					->addFieldToFilter('wishlist_id', $wishlist->getId())
					->addFieldToFilter('product_id', $productId);
			$wishlistItemId = $collection->getFirstItem()->getWishlistItemId();
		}
		return $wishlistItemId;
	}
	public function wishlistItemClass($productId) {
		$itemWishListClass = '';
		$product = Mage::getModel('catalog/product')->load($productId);
		if($this->checkItemInWishlist($productId)){
			$itemWishListClass .= ' added-item';
		}
		return $itemWishListClass;
	}
	/* public function getWishlistItemIdFromProductId($productId) {
			$wishlistItemId = null;
			$session = Mage::getSingleton('customer/session');
			if ($session->isLoggedIn()) {
					try {
							$resource = Mage::getSingleton('core/resource');
							$wishlistTable = $resource->getTableName('wishlist');
							$wishlistItemTable = $resource->getTableName('wishlist_item');

							$db = $resource->getConnection('core_read');
							$query = $db->select()
									->from(array('w' => $wishlistTable), array('wishlist_id'))
									->where('customer_id = ?', $session->getCustomer()->getId());
							$query2 = $db->select()
									->from(array('i' => $wishlistItemTable), array('wishlist_item_id'))
									->where('i.store_id = ?', Mage::app()->getStore()->getId())
									->where('i.product_id = ?', $productId)
									->join(array('w' => $query), 'w.wishlist_id = i.wishlist_id', array());
							$wishlistItemId = $db->fetchOne($query2);
					} catch (Exception $e) {

					}
			}
			return $wishlistItemId;
	} */
}
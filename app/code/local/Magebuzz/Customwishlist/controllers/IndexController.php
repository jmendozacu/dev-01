<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
require_once(Mage::getModuleDir('controllers','Mage_Wishlist').DS.'IndexController.php');
class Magebuzz_Customwishlist_IndexController extends Mage_Wishlist_IndexController {
	/**
	 * Adding new item
	 *
	 * @return Mage_Core_Controller_Varien_Action|void
	 */
	public function compareAction()
	{

		$response = array();
		if ($productId = (int) $this->getRequest()->getParam('product'))
		{
			$product = Mage::getModel('catalog/product')
				->setStoreId(Mage::app()->getStore()->getId())
				->load($productId);

			if ($product->getId()/* && !$product->isSuper() */)
			{
				Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
				$response['status'] = 'SUCCESS';
				$response['message'] = $this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()));
				$_productCollection = Mage::helper('catalog/product_compare')->getItemCollection()->count();

				if($_productCollection >=1){
					$response['popup'] = $this->getLayout()->createBlock('catalog/product_compare_list')->setTemplate('catalog/product/compare/list.phtml')->toHtml();
					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
					return;
				}else{
					$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
					return;
				}

			}
		}
	}

	protected function _getWishlist()
	{
		$wishlist = Mage::registry('wishlist');
		if ($wishlist) {

			return $wishlist;
		}

		try {
			$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
			Mage::register('wishlist', $wishlist);
		} catch (Mage_Core_Exception $e) {
			Mage::getSingleton('wishlist/session')->addError($e->getMessage());
		} catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->addException($e,
				Mage::helper('wishlist')->__('Cannot create wishlist.')
			);
			return false;
		}

		return $wishlist;
	}

	public function addAction()
	{
		$response = array();

		if(!Mage::getSingleton('customer/session')->isLoggedIn()){
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('Please Login First');
			Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account'));
			return;
		}

		if(empty($response)){
			$session = Mage::getSingleton('customer/session');
			$wishlist = $this->_getWishlist();

			if (!$wishlist) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Unable to Create Wishlist');
			}else{

				$productId = (int) $this->getRequest()->getParam('product');
				if (!$productId) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Product Not Found');
				}else{

					$product = Mage::getModel('catalog/product')->load($productId);
					if (!$product->getId() || !$product->isVisibleInCatalog()) {
						$response['status'] = 'ERROR';
						$response['message'] = $this->__('Cannot specify product.');
					}else{

						try {
							$requestParams = $this->getRequest()->getParams();
							$buyRequest = new Varien_Object($requestParams);


							Mage::helper('wishlist')->calculate();

							if(Mage::helper('customwishlist')->checkItemInWishlist($productId)){
								$message = $this->__('This product has been added to your wishlist already');
								$response['status'] = 'SUCCESS';
								$response['message'] = $message;

							}else{
								$message = $this->__('%1$s has been added to your wishlist.', $product->getName());
								$response['status'] = 'SUCCESS';
								$response['message'] = $message;
							}

							$result = $wishlist->addNewItem($product, $buyRequest);
							if (is_string($result)) {
								Mage::throwException($result);
							}
							$wishlist->save();

							Mage::dispatchEvent(
								'wishlist_add_product',
								array(
									'wishlist'  => $wishlist,
									'product'   => $product,
									'item'      => $result
								)
							);

						}
						catch (Mage_Core_Exception $e) {
							$response['status'] = 'ERROR';
							$response['message'] = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
						}
						catch (Exception $e) {
							mage::log($e->getMessage());
							$response['status'] = 'ERROR';
							$response['message'] = $this->__('An error occurred while adding item to wishlist.');
						}
					}
				}
			}

		}
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		return;
	}

	/**
	 * Add the item to wish list
	 *
	 * @return Mage_Core_Controller_Varien_Action|void
	 */
	protected function _addItemToWishList()
	{
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		$wishlist = $this->_getWishlist();
		if (!$wishlist) {
				return $this->norouteAction();
		}

		$session = Mage::getSingleton('core/session');

		$productId = (int)$this->getRequest()->getParam('product');
		if (!$productId) {
				$this->_redirect('*/');
				return;
		}

		$product = Mage::getModel('catalog/product')->load($productId);
		if (!$product->getId() || !$product->isVisibleInCatalog()) {
				$session->addError($this->__('Cannot specify product.'));
				$this->_redirect('*/');
				return;
		}

		try {
				$requestParams = $this->getRequest()->getParams();
				if ($session->getBeforeWishlistRequest()) {
						$requestParams = $session->getBeforeWishlistRequest();
						$session->unsBeforeWishlistRequest();
				}
				$buyRequest = new Varien_Object($requestParams);

				$result = $wishlist->addNewItem($product, $buyRequest);
				if (is_string($result)) {
						Mage::throwException($result);
				}
				$wishlist->save();

				Mage::dispatchEvent(
						'wishlist_add_product',
						array(
								'wishlist' => $wishlist,
								'product' => $product,
								'item' => $result
						)
				);

				$referer = $session->getBeforeWishlistUrl();
				if ($referer) {
						$session->setBeforeWishlistUrl(null);
				} else {
						$referer = $this->_getRefererUrl();
				}

				/**
				 *  Set referer to avoid referring to the compare popup window
				 */
				$session->setAddActionReferer($referer);

				Mage::helper('wishlist')->calculate();

				$message = $this->__('%1$s has been added to your wishlist. Click <a href="%2$s">here</a> to continue shopping.',
						$product->getName(), Mage::helper('core')->escapeUrl($referer));
				$session->addSuccess($message);
		} catch (Mage_Core_Exception $e) {
				$session->addError($this->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
		}
		catch (Exception $e) {
				$session->addError($this->__('An error occurred while adding item to wishlist.'));
		}
		$this->_redirectReferer($currentUrl);
	}
  public function removeallAction() {
		$customerId = Mage::getSingleton('customer/session')->getCustomerId();
		$itemCollection = Mage::getModel('wishlist/item')->getCollection()->addCustomerIdFilter($customerId);
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		$isRemoveAll = false;
		foreach($itemCollection as $item) {
			if (!$item->getId()) {
				return $this->norouteAction();
			}
			$wishlist = $this->_getWishlist($item->getWishlistId());
			if (!$wishlist) {
				return $this->norouteAction();
			}
			try {
				$item->delete();
				$wishlist->save();
				$isRemoveAll = true;
			} catch (Mage_Core_Exception $e) {
				$isRemoveAll = false;
			}
		}
		if($isRemoveAll) {
			Mage::getSingleton('core/session')->addSuccess($this->__('The wishlist was cleared.'));
		}else{
			Mage::getSingleton('customer/session')->addError($this->__('An error occurred while deleting all items in your wishlist: %s', $e->getMessage()));
		}
		Mage::helper('wishlist')->calculate();
		$this->_redirectReferer($currentUrl);
  }
	/**
	 * Remove item
	 */
	public function removeAction()
	{
			$id = (int) $this->getRequest()->getParam('item');
			$item = Mage::getModel('wishlist/item')->load($id);
			$currentUrl = Mage::helper('core/url')->getCurrentUrl();
			if (!$item->getId()) {
					return $this->norouteAction();
			}
			$wishlist = $this->_getWishlist($item->getWishlistId());
			if (!$wishlist) {
					return $this->norouteAction();
			}
			try {
					$item->delete();
					$wishlist->save();
					Mage::getSingleton('core/session')->addSuccess(
							$this->__('Item has been removed.')
					);
			} catch (Mage_Core_Exception $e) {
					Mage::getSingleton('core/session')->addError(
							$this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage())
					);
			} catch (Exception $e) {
					Mage::getSingleton('core/session')->addError(
							$this->__('An error occurred while deleting the item from wishlist.')
					);
			}

			Mage::helper('wishlist')->calculate();

			$this->_redirectReferer($currentUrl);
	}
}
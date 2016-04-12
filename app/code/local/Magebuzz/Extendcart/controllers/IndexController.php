<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
class Magebuzz_Extendcart_IndexController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

	private function _getCoreSession() {
		return Mage::getSingleton('core/session');
	}
	protected function _getSession()
	{
		return Mage::getSingleton('checkout/session');
	}

	protected function _getCart()
	{
		return Mage::getSingleton('checkout/cart');
	}
	
	public function updateqtyAction() {
		$productId = $this->getRequest()->getPost('product_id');
		$productQty = $this->getRequest()->getPost('product_qty');
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$_response = array();
		if ($productId) {
			$cart = Mage::getSingleton('checkout/cart');
			$product = Mage::getModel('catalog/product')->load($productId);

			$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
			$count = $stock->getQty();

			try{
				$cart = $this->_getCart();
				
				$quoteItem = $cart->getQuote()->getItemByProduct($product);
				if (!$quoteItem) {
						Mage::throwException($this->__('Quote item is not found.'));
				}
				if($productQty > $count){
					Mage::throwException($this->__('Over quatity in stock.'));
				}
				if ($productQty == 0) {
						$cart->removeItem($productId);
				} else {
					$_response['quote_item_id'] = $quoteItem->getId();
					$_response['cart_item_row_total'] = Mage::helper('checkout')->formatPrice($productQty * $quoteItem->getProduct()->getFinalPrice());
					$_response['cart_item_row_product_id'] = $quoteItem->getProduct()->getId();
					$_response['cart_item_row_qty'] = $productQty;
					$quoteItem->setQty($productQty)->save();
				}
				$this->_getCart()->save();
				
				if (!$quoteItem->getHasError()) {
					$_response['message'] = $this->__('Item was updated successfully.');
				} else {
					$_response['notice'] = $quoteItem->getMessage();
				}
				
        		$totalsBlock = Mage::app()->getLayout()->createBlock('checkout/cart_totals')->setTemplate('checkout/cart/totals.phtml');
				
				//$couponPointBlock = Mage::app()->getLayout()->createBlock('rewardpoints/coupon')->setTemplate('rewardpoints/cart_coupon.phtml');
				
        		$_response['totals'] = $totalsBlock->toHtml();
        //$_response['totals'] .= $couponPointBlock->toHtml();
				
				$cart_item_count = Mage::getSingleton('checkout/cart')->getSummaryQty();
				$_response['success'] = 'true';
				$_response['cart_item_count'] = $cart_item_count;
				$_response['cart_subtotal'] = Mage::helper('checkout')->formatPrice(Mage::helper('extendcart')->getCartSubtotal());
				$this->getResponse()->setBody(json_encode($_response));
				return;
			} catch (Exception $e) {
				$_response['success'] = 'false';
				$_response['cart_item_count'] = Mage::getSingleton('checkout/cart')->getSummaryQty();
				$_response['cart_subtotal'] = Mage::helper('checkout')->formatPrice(Mage::helper('extendcart')->getCartSubtotal());
				$this->getResponse()->setBody(json_encode($_response));
			}
		}
	}
	
	public function updateQtyShoppingCartAction() {
		$productId = $this->getRequest()->getPost('product_id');
		$productQty = $this->getRequest()->getPost('product_qty');
		
		$_response = array();
		if ($productId) {
			try{
				$cart = $this->_getCart();
				if (isset($productQty)) {
					$filter = new Zend_Filter_LocalizedToNormalized(
							array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$productQty = $filter->filter($productQty);
				}
				$quoteItem = $cart->getQuote()->getItemById($productId);
				if (!$quoteItem) {
						Mage::throwException($this->__('Quote item is not found.'));
				}
				if ($productQty == 0) {
						$cart->removeItem($productId);
				} else {
					$_response['cart_item_row_total'] = Mage::helper('checkout')->formatPrice($productQty * $quoteItem->getProduct()->getFinalPrice());
					$_response['cart_item_row_product_id'] = $quoteItem->getProduct()->getId();
					$_response['cart_item_row_qty'] = $productQty;
					$quoteItem->setQty($productQty)->save();
				}
				$this->_getCart()->save();
				
				if (!$quoteItem->getHasError()) {
					$_response['message'] = $this->__('Item was updated successfully.');
				} else {
					$_response['notice'] = $quoteItem->getMessage();
				}
				
        $totalsBlock = Mage::app()->getLayout()->createBlock('checkout/cart_totals')->setTemplate('checkout/cart/totals.phtml');
				
				$couponPointBlock = Mage::app()->getLayout()->createBlock('rewardpoints/coupon')->setTemplate('rewardpoints/cart_coupon.phtml');
				
        $_response['totals'] = $totalsBlock->toHtml();
        $_response['totals'] .= $couponPointBlock->toHtml();
				$_response['success'] = 'true';
				$_response['cart_item_count'] = $this->_getCart()->getSummaryQty();
				$_response['cart_subtotal'] = Mage::helper('checkout')->formatPrice(Mage::helper('extendcart')->getCartSubtotal());
			} catch (Exception $e) {
				$_response['success'] = 'false';
				$_response['cart_item_count'] = $this->_getCart()->getSummaryQty();
				$_response['cart_subtotal'] = Mage::helper('checkout')->formatPrice(Mage::helper('extendcart')->getCartSubtotal());
			}
		}
		
		$this->getResponse()->setHeader('Content-type', 'application/json');
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($_response));
	}
}
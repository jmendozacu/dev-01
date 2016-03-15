<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
require_once 'Mage/Checkout/controllers/CartController.php';
class Magebuzz_Extendcart_CartController extends Mage_Checkout_CartController {
	/**
	 * Shopping cart display action
	 */
	public function indexAction() {
		$cart = $this->_getCart();
		if ($cart->getQuote()->getItemsCount()) {
				$cart->init();
				$cart->save();

				if (!$this->_getQuote()->validateMinimumAmount()) {
						$minimumAmount = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
								->toCurrency(Mage::getStoreConfig('sales/minimum_order/amount'));

						$warning = Mage::getStoreConfig('sales/minimum_order/description')
								? Mage::getStoreConfig('sales/minimum_order/description')
								: Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount);

						$cart->getCheckoutSession()->addNotice($warning);
				}
		}

		// Compose array of messages to add
		$messages = array();
		foreach ($cart->getQuote()->getMessages() as $message) {
				if ($message) {
						// Escape HTML entities in quote message to prevent XSS
						if($message->getCode() != 'Some of the products are currently out of stock.') {
							$message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
							$messages[] = $message;
						}else{
							$allItems = $cart->getQuote()->getAllItems();
							$productNameArr = array();
							foreach ($allItems as $item) {
								if(!$item->getProduct()->getIsInStock()){
									$productNameArr[] = $item->getProduct()->getName();
								}
							}
							$message->setCode(Mage::helper('checkout')->__('There are some out of stock products: %s', implode(", ", $productNameArr)));
							$messages[] = $message;
						}
				}
		}
		$cart->getCheckoutSession()->addUniqueMessages($messages);

		/**
		 * if customer enteres shopping cart we should mark quote
		 * as modified bc he can has checkout page in another window.
		 */
		$this->_getSession()->setCartWasUpdated(true);

		Varien_Profiler::start(__METHOD__ . 'cart_display');
		$this
				->loadLayout()
				->_initLayoutMessages('checkout/session')
				->_initLayoutMessages('catalog/session')
				->getLayout()->getBlock('head')->setTitle($this->__('Shopping Cart'));
		$this->renderLayout();
		Varien_Profiler::stop(__METHOD__ . 'cart_display');
	}
}
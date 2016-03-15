<?php
class Magebuzz_Extendcart_Helper_Data extends Mage_Core_Helper_Abstract
{
  public function getShoppingCartHtml() {
    $cart = Mage::app()->getLayout()->createBlock('checkout/cart','checkout.cart');
    $cart->setTemplate('checkout/cart.phtml');
    $cart->addItemRender('simple', 'checkout/cart_item_renderer', 'checkout/cart/item/default.phtml');
    $cart->addItemRender('grouped', 'checkout/cart_item_renderer_grouped', 'checkout/cart/item/default.phtml');
    $cart->addItemRender('configurable', 'checkout/cart_item_renderer_configurable', 'checkout/cart/item/default.phtml');

    $cartCoupon = Mage::app()->getLayout()->createBlock('checkout/cart_coupon','coupon');
    $cartCoupon->setTemplate('rewardpoints/coupons.phtml');

    $rewardPoint = Mage::app()->getLayout()->createBlock('rewardpoints/coupon','coupon_points');
    $rewardPoint->setTemplate('rewardpoints/reward_coupon.phtml');
    $rewardCoupon = Mage::app()->getLayout()->createBlock('checkout/cart_coupon', 'coupon_original');
    $rewardCoupon->setTemplate('checkout/cart/coupon.phtml');
    $rewardPoint->append($rewardCoupon);
    $cartCoupon->append($rewardPoint);

    $cartShipping = Mage::app()->getLayout()->createBlock('checkout/cart_shipping','shipping');
    $cartShipping->setTemplate('checkout/cart/shipping.phtml');
    $cartCrosssell = Mage::app()->getLayout()->createBlock('checkout/cart_crosssell','crosssell');
    $cartCrosssell->setTemplate('checkout/cart/crosssell.phtml');
    $cartTotal = Mage::app()->getLayout()->createBlock('checkout/cart_totals','totals');
    $cartTotal->setTemplate('checkout/cart/totals.phtml');

    $cartTopMethods = Mage::app()->getLayout()->createBlock('core/text_list','top_methods')->setLabel('Payment Methods Before Checkout Button');
    $cartOnepageLink = Mage::app()->getLayout()->createBlock('checkout/onepage_link', 'checkout.cart.methods.onepage');
    $cartOnepageLink->setTemplate('checkout/onepage/link.phtml');
    $cartTopMethods->append($cartOnepageLink);

    $cartFormBefore = Mage::app()->getLayout()->createBlock('page/html_wrapper', 'form_before')->setLabel('Shopping Cart Form Before');

    $cartMethod = Mage::app()->getLayout()->createBlock('core/text_list', 'methods')->setLabel('Payment Methods After Checkout Button');
    $cartMethodOnepage = Mage::app()->getLayout()->createBlock('checkout/onepage_link', 'checkout.cart.methods.onepage');
    $cartMethodOnepage->setTemplate('checkout/onepage/link.phtml');
    $cartMethodMultishipping = Mage::app()->getLayout()->createBlock('checkout/multishipping_link', 'checkout.cart.methods.multishipping');
    $cartMethodMultishipping->setTemplate('checkout/multishipping/link.phtml');
    $cartMethod->append($cartMethodOnepage);
    $cartMethod->append($cartMethodMultishipping);

    $cart->append($cartCoupon);
    $cart->append($cartTotal);
    $cart->append($cartTopMethods);
    $cart->append($cartFormBefore);
    $cart->append($cartMethod);

    return $cart->toHtml();
  }

  public function getMiniCartHtml() {
    $cart_minicart = Mage::app()->getLayout()->createBlock('checkout/cart_sidebar','header.cart.mini');
    $cart_minicart->setTemplate('checkout/cart/headercart.phtml');
    $cart_minicart->addItemRender('simple', 'checkout/cart_item_renderer', 'checkout/cart/sidebar/default.phtml');
    $cart_minicart->addItemRender('grouped', 'checkout/cart_item_renderer_grouped', 'checkout/cart/sidebar/default.phtml');
    $cart_minicart->addItemRender('configurable', 'checkout/cart_item_renderer_configurable', 'checkout/cart/sidebar/default.phtml');
    $extra_actions = Mage::app()->getLayout()->createBlock('core/text_list','cart_sidebar.extra_actions')->setLabel('Shopping Cart Sidebar Extra Actions');
    $cart_minicart->append($extra_actions);

    return $cart_minicart->toHtml();
  }
	
	public function getCartSubtotal($skipTax = true) {
		$subtotal = 0;
		$totals = Mage::getModel('checkout/session')->getQuote()->getTotals();
		$config = Mage::getSingleton('tax/config');
		if (isset($totals['subtotal'])) {
				if ($config->displayCartSubtotalBoth()) {
						if ($skipTax) {
								$subtotal = $totals['subtotal']->getValueExclTax();
						} else {
								$subtotal = $totals['subtotal']->getValueInclTax();
						}
				} elseif($config->displayCartSubtotalInclTax()) {
						$subtotal = $totals['subtotal']->getValueInclTax();
				} else {
						$subtotal = $totals['subtotal']->getValue();
						if (!$skipTax && isset($totals['tax'])) {
								$subtotal+= $totals['tax']->getValue();
						}
				}
		}
		return $subtotal;
	}
}
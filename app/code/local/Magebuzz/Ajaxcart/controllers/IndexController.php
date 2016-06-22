<?php
/*
* Copyright (c) 2016 www.magebuzz.com
*/

class Magebuzz_Ajaxcart_IndexController extends Mage_Core_Controller_Front_Action {

  public function indexAction() {
    $this->loadLayout();
    $this->renderLayout();
  }

  protected function _getProduct() {
    $product_id = $this->getRequest()->getParam('product');
    if ($product_id) {
      $product = Mage::getModel('catalog/product')
      ->setStoreId(Mage::app()->getStore()->getId())
      ->load($product_id);
      if ($product->getId()) {
        return $product;
      }
    }
    return false;
  }

  public function addToCartAction() {
    /* check form key */
    $this->getResponse()->setHeader('Content-type', 'application/json');
    $_response = array();
    if (!$this->_validateFormKey()) {
      $_response['success'] = 'false';
      $_response['message'] =  $this->__('Form key is not valid');
      $this->getResponse()->setBody(json_encode($_response));
      return;
    }
    /* check product id*/
    $_params = $this->getRequest()->getParams();

    try {
      $_product = $this->_getProduct();

      /* return options popup content when product type is grouped */
      if (($_product->getTypeId() == 'grouped')&&(!isset($_params['super_group']))) {
        Mage::register('product', $_product);
        $html_popup = Mage::helper('ajaxcart')->getOptionsPopupHtml($_product);
        $_response['success'] = 'true';
        $_response['html_popup'] = $html_popup;
        $this->getResponse()->setBody(json_encode($_response));
        return;
      }

      if (($_product->getTypeId() == 'configurable')&&(!isset($_params['super_attribute']))) {
        Mage::register('product', $_product);
        $html_popup = Mage::helper('ajaxcart')->getOptionsPopupHtml($_product);
        $_response['success'] = 'true';
        $_response['html_popup'] = $html_popup;
        $this->getResponse()->setBody(json_encode($_response));
        return;
      }

      /* assign quantity add to add */
      if ((!isset($_params['qty'])) || ($_params['qty'] == '')) $_params['qty'] = $_product->getMinSaleQty();

      /* add item(s) to cart */
      try {
        $_cart = Mage::getSingleton('checkout/cart');
        $_cart->addProduct($_product,$_params);
        /* add related product(s)*/
        if ((isset($_params['related_product']))&&($_params['related_product'] != '')) {
          $_cart->addProductsByIds(explode(',', $_params['related_product']));
        }
        $_cart->save();
      } catch (Exception $e) {
        $_response['success'] = 'false';
				$html_popup_false = '<div class="block" id="ajaxcart_content_option_product">
					<a title="Close" class="ajaxcart-close" href="javascript:void(0)" onclick="ajaxCart.closeOptionsPopup();"></a>
					<div class="ajaxcart-heading"><p class="added-error-message">'.$e->getMessage().'</p></div>
				</div>';
        $_response['html_popup'] =  $html_popup_false;
        $this->getResponse()->setBody(json_encode($_response));
        return;
      }

      $_response['toplink_cart_html'] = $this->__("<span class='cart-item-count'>%d<span>",$_cart->getSummaryQty());
      if ((strcmp(Mage::getVersion(),'1.9.0.0') >= 0)) {
        // check using minicart by check magento version >= 1.9.0.1
        $_response['mini_cart_html'] = Mage::helper('ajaxcart')->getMiniCartHtml();
      }
      $_response['sidebar_cart_html'] = Mage::helper('ajaxcart')->getSidebarCartHtml();
      $_response['success'] = 'true';
      $_response['success_message'] = Mage::helper('ajaxcart')->getSuccessHtml($_product);

    } catch (Exception $e) {
      $_response['success'] = 'false';
      $_response['error_message'] =  $e->getMessage();
      $_response['error_code'] =  $e->getCode();
    }
    $this->getResponse()->setBody(json_encode($_response));
    return;
  }

  public function addWishlistItemAction() {

    /* check form key */
    $this->getResponse()->setHeader('Content-type', 'application/json');
    $_response = array();
    if (!$this->_validateFormKey()) {
      $_response['success'] = 'false';
      $_response['message'] =  $this->__('Form key is not valid');
      $this->getResponse()->setBody(json_encode($_response));
      return;
    }

    $itemId = (int) $this->getRequest()->getParam('wishlist_item_id');
    $item = Mage::getModel('wishlist/item')->load($itemId);
    $wishlist = Mage::getModel('wishlist/wishlist')->load($item->getWishlistId());
    $_product = $item->getProduct();

    if ((!$wishlist)||(!$item->getId())) {
      $_response['success'] = 'false';
      $_response['message'] =  $this->__('Invalid wishlist item');
      $this->getResponse()->setBody(json_encode($_response));
      return;
    }

    $qty = $this->getRequest()->getParam('qty');
    if (is_array($qty)) {
      if (isset($qty[$itemId])) {
        $qty = $qty[$itemId];
      } else {
        $qty = 1;
      }
    }
    if ($qty) {
      $item->setQty($qty);
    }
    /* @var $session Mage_Wishlist_Model_Session */
    $cart       = Mage::getSingleton('checkout/cart');
    try {
      $options = Mage::getModel('wishlist/item_option')->getCollection()
      ->addItemFilter(array($itemId));
      $item->setOptions($options->getOptionsByItem($itemId));

      $buyRequest = Mage::helper('catalog/product')->addParamsToBuyRequest(
      $this->getRequest()->getParams(),
      array('current_config' => $item->getBuyRequest())
      );

      $item->mergeBuyRequest($buyRequest);

      if ($item->addToCart($cart, true)) {
        $cart->save()->getQuote()->collectTotals();
      }

      $wishlist->save();

      $_response['toplink_cart_html'] = $this->__("<span class='cart-item-count'>%d<span>",$cart->getSummaryQty());
      if ((strcmp(Mage::getVersion(),'1.9.0.0') >= 0)) {
        // check using minicart by check magento version >= 1.9.0.1
        $_response['mini_cart_html'] = Mage::helper('ajaxcart')->getMiniCartHtml();
      }
      $_response['sidebar_cart_html'] = Mage::helper('ajaxcart')->getSidebarCartHtml();
      $_response['success'] = 'true';
      $_response['success_message'] = Mage::helper('ajaxcart')->getSuccessHtml($_product);

      Mage::helper('wishlist')->calculate();
      $this->getResponse()->setBody(json_encode($_response));
      return;

    } catch (Mage_Core_Exception $e) {
      if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
        // TODO: return out stock message
        $_response['success'] = 'false';
        $_response['message'] = $this->__('This product(s) is currently out of stock');
        $this->getResponse()->setBody(json_encode($_response));
        return;
      } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {      
          $_response['success'] = 'false';
          $_response['message'] = $e->getMessage();               
          $this->getResponse()->setBody(json_encode($_response));
          return;
        } else {
          // TODO: return error message
          if($_product->getTypeId() != 'bundle'){      
            $param = $item->getBuyRequest();        
            $optionValues = $_product->processBuyRequest($param);
            $optionValues->setQty($buyRequest->getQty());
            $_product->setPreconfiguredValues($optionValues);
            Mage::register('product', $_product);       
            $html_popup = Mage::helper('ajaxcart')->getOptionsPopupHtml($_product);
            $_response['success'] = 'process';
            $_response['html_popup'] = $html_popup;
          }else{
            $editWishlist = Mage::getUrl("wishlist/index/configure",array('id'=>$item->getId()));
            $_response['success'] = 'false';
            $_response['redirect_url'] = $editWishlist;
          }

          $this->getResponse()->setBody(json_encode($_response));
          return;
      }
    }
  }

  public function testAction() {
    echo Mage::helper('ajaxcart')->getMiniCartHtml();
  }
}

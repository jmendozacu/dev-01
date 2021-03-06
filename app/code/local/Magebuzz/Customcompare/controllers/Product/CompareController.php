<?php
require_once('Mage/Catalog/controllers/Product/CompareController.php');
class MageBuzz_Customcompare_Product_CompareController extends Mage_Catalog_Product_CompareController {

    public function addAction()
    {
        $response = array();
        if ($productId = (int) $this->getRequest()->getParam('product'))
        {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId() && (Mage::getSingleton('log/visitor')->getId() || Mage::getSingleton('customer/session')->isLoggedIn()))
                {
                    try {
                        $count = Mage::helper('catalog/product_compare')->getItemCount();
                        if($count<4){
                            Mage::getSingleton('catalog/product_compare_list')->addProduct($product);

                            $response['status'] = 'SUCCESS';
                            $response['message'] = $this->__('The product has been added to comparison list.');
                            Mage::register('referrer_url', $this->_getRefererUrl());
                            Mage::helper('catalog/product_compare')->calculate();
                            Mage::dispatchEvent('catalog_product_compare_add_product', array('product' => $product));
                            $this->loadLayout();
                            $response['bar'] = $this->getLayout()->createBlock('catalog/product_compare_sidebar')->setTemplate('catalog/product/compare/compare-product.phtml')->toHtml();
                            $response['html'] =
                                '<div class="block" id="ajaxcart_content_option_product">
													<a title="Close" class="ajaxcart-close" href="javascript:void(0)" onclick="ajaxCart.closeOptionsPopup();"></a>
													<div class="ajaxcart-heading">
														<p class="added-success-message">'
                                . $response['message'] .
                                '</p>
													</div>
												</div>
												';
                        }else{
                            $response['message'] = $this->__(' You can add product to compare maximum 4 products');
                            $response['html'] =
                                '<div class="block" id="ajaxcart_content_option_product">
													<a title="Close" class="ajaxcart-close" href="javascript:void(0)" onclick="ajaxCart.closeOptionsPopup();"></a>
													<ul class="messages">
                                                        <li class="error-msg">
                                                            <ul>
                                                                <li>
                                                                     <span>' . $response['message'] . '</span>
                                                                 </li>
                                                            </ul>
                                                          </li>
                                                      </ul>
												</div>
												';
                        }
                    }
                    catch (Exception $e) {
                        $response['status'] = 'ERROR';
                        $response['error'] = $this->__('Can not add to comparison list.');
                    }
                }

        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }

    public function removeAction()
    {
        $response = array();
        if ($productId = (int) $this->getRequest()->getParam('product')) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            if($product->getId()) {
                /** @var $item Mage_Catalog_Model_Product_Compare_Item */
                $item = Mage::getModel('catalog/product_compare_item');
                if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
                } elseif ($this->_customerId) {
                    $item->addCustomerData(
                        Mage::getModel('customer/customer')->load($this->_customerId)
                    );
                } else {
                    $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
                }

                $item->loadByProduct($product);

                if($item->getId()) {
                    $item->delete();
                    Mage::dispatchEvent('catalog_product_compare_remove_product', array('product'=>$item));
                    Mage::helper('catalog/product_compare')->calculate();
                    $this->loadLayout();
                    $response['bar'] = $this->getLayout()->createBlock('catalog/product_compare_sidebar')->setTemplate('catalog/product/compare/compare-product.phtml')->toHtml();
                }
            }
        }
        $response['status'] = 'SUCCESS';
        $response['message'] = $this->__('The product has been removed to comparison list.');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
        }

    public function clearAction()
    {

        $items = Mage::getResourceModel('catalog/product_compare_item_collection');

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
        } elseif ($this->_customerId) {
            $items->setCustomerId($this->_customerId);
        } else {
            $items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
        }

        /** @var $session Mage_Catalog_Model_Session */
        $session = Mage::getSingleton('catalog/session');

        try {
            $items->clear();
            $response['status'] = 'SUCCESS';
            $response['message'] = $this->__('The comparison list was cleared.');
            Mage::helper('catalog/product_compare')->calculate();
            $this->loadLayout();
            $response['bar'] = $this->getLayout()->createBlock('catalog/product_compare_sidebar')->setTemplate('catalog/product/compare/sidebar.phtml')->toHtml();
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $this->__('An error occurred while clearing comparison list.'));
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }
}
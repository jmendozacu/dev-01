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
                        Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
                        $response['status'] = 'SUCCESS';
                        $response['message'] = $this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()));
                        $response['name'] =  Mage::helper('core')->escapeHtml($product->getName());
                        $response['url'] = $product->getProductUrl();
                        $response['bar'] = $this->getLayout()->createBlock('catalog/product_compare_sidebar')->setTemplate('catalog/product/compare/sidebar.phtml')->toHtml();
                        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
                    }
                    catch (Exception $e) {
                        echo $e->getMessage();
                        die('aa');
                    }
                }
        }
    }
}
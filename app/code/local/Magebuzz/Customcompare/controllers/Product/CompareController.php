<?php
require_once(Mage::getModuleDir('controllers','Mage_Catalog').DS.'controllers'.DS.'Product'.DS.'CompareController.php');
class MageBuzz_Customcompare_Product_CompareController extends Mage_Catalog_Product_CompareController {

    public function addAction()
    {

        die('22');
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
}
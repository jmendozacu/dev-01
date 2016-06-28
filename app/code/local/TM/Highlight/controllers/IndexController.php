<?php
/**
 * This is the part of 'Highlight' module for Magento,
 * which allows easy access to product collection
 * with flexible filters
 *
 * @author Templates-Master
 * @copyright Templates Master www.templates-master.com
 */

class TM_Highlight_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $type = $this->getRequest()->getParam('type');
        $type = trim($type, '/ ');
        $typeMapping = array(
            TM_Highlight_Block_Product_New::PAGE_TYPE        => 'highlight/product_new',
            TM_Highlight_Block_Product_Special::PAGE_TYPE    => 'highlight/product_special',
            TM_Highlight_Block_Product_Featured::PAGE_TYPE   => 'highlight/product_featured',
            TM_Highlight_Block_Product_Bestseller::PAGE_TYPE => 'highlight/product_bestseller',
            TM_Highlight_Block_Product_Popular::PAGE_TYPE    => 'highlight/product_popular'
        );
        if (!isset($typeMapping[$type])) {
            return $this->_forward('noRoute');
        }

        if ($this->getRequest()->getQuery('type')) {
            $urlKey = Mage::helper('highlight')->getPageUrlKey($type);
            if ($urlKey) {
                // https://www.ltnow.com/difference-301-302-redirects-seo/
                return $this->getResponse()->setRedirect(
                    Mage::getModel('core/url')->getDirectUrl($urlKey), 301
                );
            }
        }

        $this->loadLayout();
        $layout = $this->getLayout();
        $list   = $layout->getBlock('product_list');
        $block  = $layout->createBlock($typeMapping[$type])
            ->setNameInLayout('highlight_collection');

        if (!$block || !$list) {
            return $this->_forward('noRoute');
        }

        if (method_exists($block, 'getPeriod')) {
            $block->setPeriod(Mage::getStoreConfig("highlight/pages/{$type}_period"));
        }
        $block->setTitle(Mage::getStoreConfig("highlight/pages/{$type}_title"));
        $list->setCollectionBlock($block);

        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->__($list->getTitle()));
            $headBlock->addLinkRel('canonical', $block->getPageUrl());
        }

        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }
    public function getOptionRelatedAvailableAction(){

        $this->getResponse()->setHeader('Content-type','application/json');
        $response = array();

        $storeId = Mage::app()->getStore()->getId();

        $productId = $this->getRequest()->getParam('productId');
        $attributeId = $this->getRequest()->getParam('attributeId');
        $value = $this->getRequest()->getParam('value');
        $positionAttribute = $this->getRequest()->getParam('positionAttribute');
        $label = $this->getRequest()->getParam('label');
        $attributeSelected = $this->getRequest()->getParam('attributeSelected');


        $product = Mage::getModel('catalog/product')->load($productId);

        $allowedProduct = $product->getTypeInstance(true)
            ->getConfigurableAttributes($product);

        $childProducts = Mage::getModel('catalog/product_type_configurable')
            ->getUsedProductCollection($product)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter($label, $value);
        if($attributeSelected){
            $arrayattributeSelected = explode('-',$attributeSelected);
            $childProducts->addAttributeToFilter($arrayattributeSelected[0], $arrayattributeSelected['1']);
        }

        $arrayAttributeAdd = array();
        $idTagARemove = array();
        foreach($allowedProduct as $attribute){
            $productAttribute   = $attribute->getProductAttribute();
            $productAttributeId = $productAttribute->getId();
            if(($positionAttribute + 1) == $attribute->getPosition()){
                $id = array();
                $prices = $attribute->getPrices();
                if (is_array($prices)) {
                    foreach($prices as $valueOption){
                        $id[] =$valueOption['value_index'];
                    }
                }
                $arrayValueOfAttribute = array();
                $childProducts->addAttributeToSelect($attribute->getLabel());
                foreach($childProducts as $_childProductAvailable){
                    $valueOfAttribute =  Mage::getResourceModel('catalog/product')->getAttributeRawValue($_childProductAvailable->getId(), $attribute->getLabel(), $storeId);
                    $arrayValueOfAttribute[] = $valueOfAttribute;
                    $arrayAttributeAdd[$valueOfAttribute] = "link-related-customgroup-item-".$productId."-attribute".$productAttributeId."-".$valueOfAttribute."" ;
                }

                $arrayOptionRemove = array_diff($id, $arrayValueOfAttribute);
                foreach($arrayOptionRemove as $key =>$valueOptionInArray){
                    if($valueOptionInArray){
                        $idTagARemove[$valueOptionInArray] = "link-related-customgroup-item-".$productId."-attribute".$productAttributeId."-".$valueOptionInArray."" ;
                    }
                }
            }
        }

        $arrayAttributeAdd =  array_unique($arrayAttributeAdd);
        $response['resultUpdate'] = $arrayAttributeAdd;
        $response['resultRemove'] = $idTagARemove;
        $response['success'] = 'true';

        $this->getResponse()->setBody(json_encode($response));
    }
    public function getOptionMayLikeAvailableAction(){

        $this->getResponse()->setHeader('Content-type','application/json');
        $response = array();

        $storeId = Mage::app()->getStore()->getId();

        $productId = $this->getRequest()->getParam('productId');
        $attributeId = $this->getRequest()->getParam('attributeId');
        $value = $this->getRequest()->getParam('value');
        $positionAttribute = $this->getRequest()->getParam('positionAttribute');
        $label = $this->getRequest()->getParam('label');
        $attributeSelected = $this->getRequest()->getParam('attributeSelected');


        $product = Mage::getModel('catalog/product')->load($productId);

        $allowedProduct = $product->getTypeInstance(true)
            ->getConfigurableAttributes($product);

        $childProducts = Mage::getModel('catalog/product_type_configurable')
            ->getUsedProductCollection($product)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter($label, $value);
        if($attributeSelected){
            $arrayattributeSelected = explode('-',$attributeSelected);
            $childProducts->addAttributeToFilter($arrayattributeSelected[0], $arrayattributeSelected['1']);
        }

        $arrayAttributeAdd = array();
        $idTagARemove = array();
        foreach($allowedProduct as $attribute){
            $productAttribute   = $attribute->getProductAttribute();
            $productAttributeId = $productAttribute->getId();
            if(($positionAttribute + 1) == $attribute->getPosition()){
                $id = array();
                $prices = $attribute->getPrices();
                if (is_array($prices)) {
                    foreach($prices as $valueOption){
                        $id[] =$valueOption['value_index'];
                    }
                }
                $arrayValueOfAttribute = array();
                $childProducts->addAttributeToSelect($attribute->getLabel());
                foreach($childProducts as $_childProductAvailable){
                    $valueOfAttribute =  Mage::getResourceModel('catalog/product')->getAttributeRawValue($_childProductAvailable->getId(), $attribute->getLabel(), $storeId);
                    $arrayValueOfAttribute[] = $valueOfAttribute;
                    $arrayAttributeAdd[$valueOfAttribute] = "link-maylike-customgroup-item-".$productId."-attribute".$productAttributeId."-".$valueOfAttribute."" ;
                }

                $arrayOptionRemove = array_diff($id, $arrayValueOfAttribute);
                foreach($arrayOptionRemove as $key =>$valueOptionInArray){
                    if($valueOptionInArray){
                        $idTagARemove[$valueOptionInArray] = "link-maylike-customgroup-item-".$productId."-attribute".$productAttributeId."-".$valueOptionInArray."" ;
                    }
                }
            }
        }

        $arrayAttributeAdd =  array_unique($arrayAttributeAdd);
        $response['resultUpdate'] = $arrayAttributeAdd;
        $response['resultRemove'] = $idTagARemove;
        $response['success'] = 'true';

        $this->getResponse()->setBody(json_encode($response));
    }
    public function getOptionViewAllAvailableAction(){

        $this->getResponse()->setHeader('Content-type','application/json');
        $response = array();

        $storeId = Mage::app()->getStore()->getId();

        $productId = $this->getRequest()->getParam('productId');
        $attributeId = $this->getRequest()->getParam('attributeId');
        $value = $this->getRequest()->getParam('value');
        $positionAttribute = $this->getRequest()->getParam('positionAttribute');
        $label = $this->getRequest()->getParam('label');
        $attributeSelected = $this->getRequest()->getParam('attributeSelected');


        $product = Mage::getModel('catalog/product')->load($productId);

        $allowedProduct = $product->getTypeInstance(true)
            ->getConfigurableAttributes($product);

        $childProducts = Mage::getModel('catalog/product_type_configurable')
            ->getUsedProductCollection($product)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter($label, $value);
        if($attributeSelected){
            $arrayattributeSelected = explode('-',$attributeSelected);
            $childProducts->addAttributeToFilter($arrayattributeSelected[0], $arrayattributeSelected['1']);
        }

        $arrayAttributeAdd = array();
        $idTagARemove = array();
        foreach($allowedProduct as $attribute){
            $productAttribute   = $attribute->getProductAttribute();
            $productAttributeId = $productAttribute->getId();
            if(($positionAttribute + 1) == $attribute->getPosition()){
                $id = array();
                $prices = $attribute->getPrices();
                if (is_array($prices)) {
                    foreach($prices as $valueOption){
                        $id[] =$valueOption['value_index'];
                    }
                }
                $arrayValueOfAttribute = array();
                $childProducts->addAttributeToSelect($attribute->getLabel());
                foreach($childProducts as $_childProductAvailable){
                    $valueOfAttribute =  Mage::getResourceModel('catalog/product')->getAttributeRawValue($_childProductAvailable->getId(), $attribute->getLabel(), $storeId);
                    $arrayValueOfAttribute[] = $valueOfAttribute;
                    $arrayAttributeAdd[$valueOfAttribute] = "link-viewall-customgroup-item-".$productId."-attribute".$productAttributeId."-".$valueOfAttribute."" ;
                }

                $arrayOptionRemove = array_diff($id, $arrayValueOfAttribute);
                foreach($arrayOptionRemove as $key =>$valueOptionInArray){
                    if($valueOptionInArray){
                        $idTagARemove[$valueOptionInArray] = "link-viewall-customgroup-item-".$productId."-attribute".$productAttributeId."-".$valueOptionInArray."" ;
                    }
                }
            }
        }

        $arrayAttributeAdd =  array_unique($arrayAttributeAdd);
        $response['resultUpdate'] = $arrayAttributeAdd;
        $response['resultRemove'] = $idTagARemove;
        $response['success'] = 'true';

        $this->getResponse()->setBody(json_encode($response));
    }
    public function getOptionBestSellAvailableAction(){

        $this->getResponse()->setHeader('Content-type','application/json');
        $response = array();

        $storeId = Mage::app()->getStore()->getId();

        $productId = $this->getRequest()->getParam('productId');
        $attributeId = $this->getRequest()->getParam('attributeId');
        $value = $this->getRequest()->getParam('value');
        $positionAttribute = $this->getRequest()->getParam('positionAttribute');
        $label = $this->getRequest()->getParam('label');
        $attributeSelected = $this->getRequest()->getParam('attributeSelected');


        $product = Mage::getModel('catalog/product')->load($productId);

        $allowedProduct = $product->getTypeInstance(true)
            ->getConfigurableAttributes($product);

        $childProducts = Mage::getModel('catalog/product_type_configurable')
            ->getUsedProductCollection($product)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter($label, $value);
        if($attributeSelected){
            $arrayattributeSelected = explode('-',$attributeSelected);
            $childProducts->addAttributeToFilter($arrayattributeSelected[0], $arrayattributeSelected['1']);
        }

        $arrayAttributeAdd = array();
        $idTagARemove = array();
        foreach($allowedProduct as $attribute){
            $productAttribute   = $attribute->getProductAttribute();
            $productAttributeId = $productAttribute->getId();
            if(($positionAttribute + 1) == $attribute->getPosition()){
                $id = array();
                $prices = $attribute->getPrices();
                if (is_array($prices)) {
                    foreach($prices as $valueOption){
                        $id[] =$valueOption['value_index'];
                    }
                }
                $arrayValueOfAttribute = array();
                $childProducts->addAttributeToSelect($attribute->getLabel());
                foreach($childProducts as $_childProductAvailable){
                    $valueOfAttribute =  Mage::getResourceModel('catalog/product')->getAttributeRawValue($_childProductAvailable->getId(), $attribute->getLabel(), $storeId);
                    $arrayValueOfAttribute[] = $valueOfAttribute;
                    $arrayAttributeAdd[$valueOfAttribute] = "link-bestsell-customgroup-item-".$productId."-attribute".$productAttributeId."-".$valueOfAttribute."" ;
                }

                $arrayOptionRemove = array_diff($id, $arrayValueOfAttribute);
                foreach($arrayOptionRemove as $key =>$valueOptionInArray){
                    if($valueOptionInArray){
                        $idTagARemove[$valueOptionInArray] = "link-bestsell-customgroup-item-".$productId."-attribute".$productAttributeId."-".$valueOptionInArray."" ;
                    }
                }
            }
        }

        $arrayAttributeAdd =  array_unique($arrayAttributeAdd);
        $response['resultUpdate'] = $arrayAttributeAdd;
        $response['resultRemove'] = $idTagARemove;
        $response['success'] = 'true';

        $this->getResponse()->setBody(json_encode($response));
    }
}

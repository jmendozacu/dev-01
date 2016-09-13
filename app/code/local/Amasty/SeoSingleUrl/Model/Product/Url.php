<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */

class Amasty_SeoSingleUrl_Model_Product_Url extends Mage_Catalog_Model_Product_Url
{
    public function getUrl(Mage_Catalog_Model_Product $product, $params = array())
    {
        $url = Mage::helper('amseourl/product_url_rewrite')->getProductPath($product);

        if (!$url)
            return parent::getUrl($product, $params);

        $params['_direct'] = $url;

        return rtrim(Mage::getUrl('', $params), '/');
    }
    protected function _getRequestPath($product, $categoryId)
    {
        $idPath = sprintf('product/%d', $product->getEntityId());
        $urlVal = Mage::getStoreConfig('catalog/seo/product_use_categories');
        if($urlVal == 0) {
            $categoryId = '';
        }
        if ($categoryId) {
            $idPath = sprintf('%s/%d', $idPath, $categoryId);
        }
        $rewrite = $this->getUrlRewrite();


        $rewrite->setStoreId($product->getStoreId())
            ->loadByIdPath($idPath);

        if ($rewrite->getId()) {
            return $rewrite->getRequestPath();
        }

        return false;
    }
}

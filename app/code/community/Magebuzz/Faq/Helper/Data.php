<?php

/**
 * @copyright   Copyright (c) 2013 AZeBiz Co. LTD
 */
class Magebuzz_Faq_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_GET_CONFIG_USING_JAVASCRIPT = 'faq/general/using_javascript';

    public function getUseJavascript()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_GET_CONFIG_USING_JAVASCRIPT);
    }

    public function generateUrl($string)
    {
        $identifier = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($string));
        $identifier = strtolower($identifier);
        $identifier = trim($identifier, '-');
        return $identifier;
    }

    public function checkUrlRewriteHasExist($tagert_path)
    {
        $oUrlRewriteCollection = Mage::getModel('core/url_rewrite')
            ->getCollection()
            ->addFieldToFilter('target_path', $tagert_path)
            ->getFirstItem();
        $id = $oUrlRewriteCollection->getId();
        $model = Mage::getModel('core/url_rewrite');
        if (count($oUrlRewriteCollection) > 0) {
            $data = $model->load($id)->delete();
        }
    }

    public function convertArrayToString()
    {
        $array = array("firstname" => "John", "lastname" => "doe");
        $json = json_encode($array);
        $phpStringArray = str_replace(array("{", "}", ":"), array("array(", "}", "=>"), $json);
        echo $phpStringArray;
    }

    public function getCategoryIdByFaqId($faq_id)
    {
        $object = Mage::getModel('faq/item')->getCollection()
            ->addFieldToFilter('faq_id', $faq_id)
            ->getFirstItem();
        return $object->getCategoryId();
    }

    public function recursiveReplace($search, $replace, $subject)
    {
        if (!is_array($subject))
            return $subject;

        foreach ($subject as $key => $value)
            if (is_string($value))
                $subject[$key] = str_replace($search, $replace, $value);
            elseif (is_array($value))
                $subject[$key] = self::recursiveReplace($search, $replace, $value);

        return $subject;
    }

  public function getHeaderPage(){
    return Mage::getStoreConfig('faq/general/page_title');
  }

    public function getIdbyQuestion($question){
        $id=null;
        $faqs = Mage::getModel('faq/faq')->getCollection();
        foreach($faqs as $faq){
            if($faq->getQuestion()==$question){
                $id = $faq->getId();
            }
        }
        return $id;
    }
     
}
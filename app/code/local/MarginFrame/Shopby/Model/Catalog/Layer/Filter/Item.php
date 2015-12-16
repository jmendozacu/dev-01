<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */

/**
 * Class MarginFrame_Shopby_Model_Catalog_Layer_Filter_Item
 * @method int getCount()
 */
class MarginFrame_Shopby_Model_Catalog_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
    public function getUrl()
    {
        Varien_Profiler::start('amshopby_filter_item_url');

        /** @var MarginFrame_Shopby_Model_Url_Builder $urlBuilder */
        $urlBuilder = Mage::getModel('amshopby/url_builder');
        $urlBuilder->reset();
        $urlBuilder->clearPagination();

        $urlBuilder->changeQuery(array(
            $this->getFilter()->getRequestVar() => $this->getValue(),
        ));

        $url = $urlBuilder->getUrl();

        Varien_Profiler::stop('amshopby_filter_item_url');
        return $url;
    }
    
    
    public function getRemoveUrl()
    {
        Varien_Profiler::start('amshopby_filter_item_url');

        /** @var MarginFrame_Shopby_Model_Url_Builder $urlBuilder */
        $urlBuilder = Mage::getModel('amshopby/url_builder');
        $urlBuilder->reset();
        $urlBuilder->clearPagination();

        $urlBuilder->changeQuery(array(
            $this->getFilter()->getRequestVar() => $this->getFilter()->getResetValue(),
        ));

        $url = $urlBuilder->getUrl();

        Varien_Profiler::stop('amshopby_filter_item_url');
        return $url;
    }

}
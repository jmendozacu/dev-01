<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
class MarginFrame_Shopby_Block_Search_Layer extends MarginFrame_Shopby_Block_Catalog_Layer_View
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        Mage::register('current_layer', $this->getLayer(), true);
    } 
    
    /**
     * Get attribute filter block name
     *
     * @return string
     */
    protected function _getAttributeFilterBlockName()
    {
        return 'catalogsearch/layer_filter_attribute';
    }

    /**
     * Get layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        return Mage::getSingleton('catalogsearch/layer');
    }

    
    public function canShowBlock()
    {
        /** @var Enterprise_Search_Model_Resource_Engine $engine */
        $engine = Mage::helper('catalogsearch')->getEngine();

        if (method_exists($engine, 'isLayeredNavigationAllowed')){
            $allowed = $engine->isLayeredNavigationAllowed();
        } else {
            $allowed = $engine->isLeyeredNavigationAllowed();
        }

        return $allowed && parent::canShowBlock();
    }
}
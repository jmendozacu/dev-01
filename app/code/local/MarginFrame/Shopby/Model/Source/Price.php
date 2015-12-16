<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */  
class MarginFrame_Shopby_Model_Source_Price extends MarginFrame_Shopby_Model_Source_Abstract
{
    public function toOptionArray()
    {
        $hlp = Mage::helper('amshopby');
        return array(
            array('value' => MarginFrame_Shopby_Model_Catalog_Layer_Filter_Price::DT_DEFAULT,    'label' => $hlp->__('Default')),
            array('value' => MarginFrame_Shopby_Model_Catalog_Layer_Filter_Price::DT_DROPDOWN,   'label' => $hlp->__('Dropdown')),
            array('value' => MarginFrame_Shopby_Model_Catalog_Layer_Filter_Price::DT_FROMTO,     'label' => $hlp->__('From-To Only')),
            array('value' => MarginFrame_Shopby_Model_Catalog_Layer_Filter_Price::DT_SLIDER,     'label' => $hlp->__('Slider')),
        );
    }
}
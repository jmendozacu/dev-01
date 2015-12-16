<?php

/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
if (method_exists('Mage', 'getEdition')) { // CE 1.7+, EE 1.12+

    if (Mage::helper('amshopby')->useSolr()) {
        class MarginFrame_Shopby_Model_Catalog_Layer_Filter_Price_Price17ce_Parent extends Enterprise_Search_Model_Catalog_Layer_Filter_Price {};
    }
    else {
        class MarginFrame_Shopby_Model_Catalog_Layer_Filter_Price_Price17ce_Parent extends Mage_Catalog_Model_Layer_Filter_Price {};
    }
    
    class MarginFrame_Shopby_Model_Catalog_Layer_Filter_Price_Adapter extends MarginFrame_Shopby_Model_Catalog_Layer_Filter_Price_Price17ce
    {
    }
} 
else { // CE 1.3.2 - 1.6.2 

    class MarginFrame_Shopby_Model_Catalog_Layer_Filter_Price_Adapter extends MarginFrame_Shopby_Model_Catalog_Layer_Filter_Price_Price14ce
    {
    }
}

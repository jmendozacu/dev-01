<?php

/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
if (Mage::helper('amshopby')->useSolr()) {
    class MarginFrame_Shopby_Model_Catalog_Layer_Filter_Category_Adapter extends Enterprise_Search_Model_Catalog_Layer_Filter_Category {}
} else {
    class MarginFrame_Shopby_Model_Catalog_Layer_Filter_Category_Adapter extends Mage_Catalog_Model_Layer_Filter_Category {}
}
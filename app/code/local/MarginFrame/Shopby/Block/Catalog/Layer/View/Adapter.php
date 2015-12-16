<?php

/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */
if (Mage::helper('amshopby')->useSolr()) {
    class MarginFrame_Shopby_Block_Catalog_Layer_View_Adapter extends Enterprise_Search_Block_Catalog_Layer_View {}
} else {
    class MarginFrame_Shopby_Block_Catalog_Layer_View_Adapter extends Mage_Catalog_Block_Layer_View {}
}
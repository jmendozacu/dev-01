<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */


class MarginFrame_Shopby_Block_Search_Layer_Filter_Attribute extends MarginFrame_Shopby_Block_Catalog_Layer_Filter_Attribute
{
    public function __construct()
    {
        parent::__construct();
        $this->_filterModelName = 'catalogsearch/layer_filter_attribute';  
    }
}
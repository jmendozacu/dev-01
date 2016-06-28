<?php
class MarginFrame_Custom_Block_Adminhtml_Catalog_Product_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $val = Mage::helper('catalog/image')->init($row, 'thumbnail')->resize(97);
        $out = "<img src=". $val ." width='97px'/>";
        return $out;
    }
}
<?php
class MarginFrame_Custom_Block_Adminhtml_Catalog_Product_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $image = Mage::helper('catalog/image')->init($row, 'image')->resize(97);
        $out = "<img src=". $image ." width='97px' /><span style='font-size:10px'>".$value."</span>";
        return $out;
    }
}
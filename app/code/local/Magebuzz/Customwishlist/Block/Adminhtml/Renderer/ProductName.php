<?php
/**
 * Created by PhpStorm.
 * User: Rekindle
 * Date: 5/16/2016
 * Time: 6:04 PM
 */
class Magebuzz_Customwishlist_Block_Adminhtml_Renderer_ProductName extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    /**
     * Thumbnail images renderer
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        $_product = Mage::getModel('catalog/product')->load($row->getData('product_id'));
        $productName = $_product->getName();
        return $productName;
    }
}
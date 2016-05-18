<?php
/**
 * Created by PhpStorm.
 * User: Rekindle
 * Date: 5/16/2016
 * Time: 6:04 PM
 */
class Magebuzz_Customwishlist_Block_Adminhtml_Renderer_CustomerName extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    /**
     * Thumbnail images renderer
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        $model = Mage::getModel('customer/customer')->load($row->getData('customer_id'));
        $name = $model->getName();
        //Mage::log($row->getData('customer_id').''.$name);
        return $name;
    }
}
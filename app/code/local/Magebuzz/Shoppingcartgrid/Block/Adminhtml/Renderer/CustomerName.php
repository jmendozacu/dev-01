<?php
/**
 * Created by PhpStorm.
 * User: Rekindle
 * Date: 5/18/2016
 * Time: 6:09 PM
 */

class Magebuzz_Shoppingcartgrid_Block_Adminhtml_Renderer_CustomerName extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    /**
     * Thumbnail images renderer
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        if ($row->getData('customer_firstname') != NULL || $row->getData('customer_lastname') != NULL) {
            $firstName = $row->getData('customer_firstname');
            $lastName = $row->getData('customer_lastname');
            if ($lastName != NULL) {
                return $firstName . ' ' . $lastName;
            } else {
                return $firstName;
            }
        } else {
            return Mage::helper('dailydeal')->__('NO NAME ASSIGNED');
        }
    }
}
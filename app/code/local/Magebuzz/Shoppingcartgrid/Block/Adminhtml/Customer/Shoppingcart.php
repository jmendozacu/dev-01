<?php
/**
 * Created by PhpStorm.
 * User: Rekindle
 * Date: 5/16/2016
 * Time: 10:49 AM
 */
class Magebuzz_Shoppingcartgrid_Block_Adminhtml_Customer_Shoppingcart extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'magebuzz_shoppingcartgrid';
        $this->_controller = 'adminhtml_customer_shoppingcart';
        $this->_headerText = Mage::helper('magebuzz_shoppingcartgrid')->__('Customer Shoppingcart');

        parent::__construct();
        $this->_removeButton('add');
    }
}
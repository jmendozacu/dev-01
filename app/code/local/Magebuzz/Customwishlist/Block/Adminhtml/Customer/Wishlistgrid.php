<?php
/**
 * Created by PhpStorm.
 * User: Rekindle
 * Date: 5/16/2016
 * Time: 10:49 AM
 */
class Magebuzz_Customwishlist_Block_Adminhtml_Customer_Wishlistgrid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'customwishlist';
        $this->_controller = 'adminhtml_wishlistgrid';
        $this->_headerText = Mage::helper('customwishlist')->__('Customer Wishlist');

        parent::__construct();
        $this->_removeButton('add');
    }
}
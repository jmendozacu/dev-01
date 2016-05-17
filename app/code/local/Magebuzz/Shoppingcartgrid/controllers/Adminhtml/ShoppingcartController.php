<?php
/**
 * Created by PhpStorm.
 * User: Rekindle
 * Date: 5/16/2016
 * Time: 10:39 AM
 */

class Magebuzz_Shoppingcartgrid_Adminhtml_ShoppingcartController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Customer'))->_title($this->__('Shopping cart'));
        $this->loadLayout();
        $this->_setActiveMenu('customer/shoppingcart');
        $this->_addContent($this->getLayout()->createBlock('magebuzz_shoppingcartgrid/adminhtml_customer_shoppingcart'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('magebuzz_shoppingcartgrid/adminhtml_customer_shoppingcart_grid')->toHtml()
        );
    }


}
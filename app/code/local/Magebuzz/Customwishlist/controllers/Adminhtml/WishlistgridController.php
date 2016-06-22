<?php
/**
 * Created by PhpStorm.
 * User: Rekindle
 * Date: 5/16/2016
 * Time: 10:39 AM
 */

class Magebuzz_Customwishlist_Adminhtml_WishlistgridController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        /*echo $this->__('Our News module is ready');*/
        $this->_title($this->__('Customer'))->_title($this->__('Customer Wishlist'));
        $this->loadLayout();
        $this->_setActiveMenu('customer');
        $this->_addContent($this->getLayout()->createBlock('customwishlist/adminhtml_customer_wishlistgrid'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('customwishlist/adminhtml_customer_wishlistgrid_grid')->toHtml()
        );
    }

    public function exportWishlistCsvAction()
    {
        $fileName = 'wishlist.csv';
        $grid = $this->getLayout()->createBlock('customwishlist/adminhtml_customer_wishlistgrid_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function exportWishlistExcelAction()
    {
        $fileName = 'wishlist.xml';
        $grid = $this->getLayout()->createBlock('customwishlist/adminhtml_customer_wishlistgrid_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }


}
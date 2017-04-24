<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Adminhtml_FlippingbookController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->_redirect('*/flippingbook_category/index');
    }


    public function productAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('flippingbook/adminhtml_catalog_product_edit_tab_magazine_grid')->toHtml());
    }

    public function attachAction()
    {
        $product_id       = (int)$this->getRequest()->getParam('product_id');
        $magazine_id      = (array)$this->getRequest()->getParam('magazine_id');
        $product_attached = (int)$this->getRequest()->getParam('product_attached');

        $message = $this->_attach($product_id, $magazine_id, $product_attached);
        $this->getResponse()->setBody($message);
    }

    public function massAttachAction()
    {
        $product_id     = $this->getRequest()->getParam('id');
        $attachtableIds = $this->getRequest()->getParam('attachtable');
        if (!is_array($attachtableIds)) {
            $message = $this->__('Please select book(s)');
        } else {
            $message = $this->_attach($product_id, $attachtableIds, 0);
        }

        die($message);
    }

    public function massDetachAction()
    {
        $product_id     = $this->getRequest()->getParam('id');
        $attachtableIds = $this->getRequest()->getParam('attachtable');
        if (!is_array($attachtableIds)) {
            $message = $this->__('Please select book(s)');
        } else {
            $message = $this->_attach($product_id, $attachtableIds, 1);
        }

        die($message);
    }

    protected function _attach($product_id, $magazine_ids, $action_type)
    {
        if ($action_type) {
            $method_name = 'detachMagazines';
            $message     = $this->__('Total of %d record(s) were detached', count($magazine_ids));
        } else {
            $method_name = 'attachMagazines';
            $message     = $this->__('Total of %d record(s) were attached', count($magazine_ids));
        }

        try {
            Mage::getResourceModel('flippingbook/magazine')->$method_name($product_id, $magazine_ids);
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        return $message;
    }

}

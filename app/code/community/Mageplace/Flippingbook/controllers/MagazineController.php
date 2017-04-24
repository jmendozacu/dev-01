<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_MagazineController extends Mage_Core_Controller_Front_Action
{
    protected $_magazine;

    protected function _initMagazine()
    {
        if(!$this->_magazine) {
            $this->_magazine = Mage::registry('flippingbook_current_magazine');
            if(!$this->_magazine) {
                $magazine_id = (int) $this->getRequest()->getParam('id', false);
                if (!$magazine_id) {
                    return false;
                }

                $this->_magazine = Mage::getModel('flippingbook/magazine')->load($magazine_id);

                if (!Mage::helper('flippingbook')->canShowMagazine($this->_magazine)) {
                    return false;
                }

                if($this->getRequest()->getActionName() == 'popup') {
                    $this->_magazine->setData('is_popup_view', true);
                } else {
                    $this->_magazine->setData('is_popup_view', false);
                }

                Mage::register('flippingbook_current_magazine', $this->_magazine);
            }
        }


        return $this->_magazine;
    }

    public function viewAction()
    {
        if ($this->_initMagazine()) {
            $this->loadLayout();
            $root_template = Mage::helper('flippingbook')->getRootTemplate();
            if($root_template){
                $this->getLayout()->getBlock('root')->setTemplate($root_template);
            }
            $this->renderLayout();
        } else {
            $this->_forward('noRoute');
        }
    }

    public function popupAction()
    {
        $this->viewAction();
    }
}

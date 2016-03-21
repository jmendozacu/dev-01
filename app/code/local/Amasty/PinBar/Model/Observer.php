<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_PinBar
 */


class Amasty_PinBar_Model_Observer
{
    /**
     * @param $observer
     */
    public function saveSessionData($observer) {
        if(Mage::helper('ampinbar')->moduleEnabled()) {
            $block = $observer->getBlock();
            $transport = $observer->getTransport();
            $html = $transport->getHtml();
            if ($block instanceof Mage_Adminhtml_Block_Widget_Container)
            {
                $block = $block->getChild('grid');
                if ($block) {
                    $this->addParams($block);

                    $request = Mage::app()->getRequest();
                    $path = $request->getRouteName() . '/' . $request->getControllerName() . '/' . $request->getActionName();
                    $addToJs = $block->getJsObjectName() . ".reloadParams['ampinbar_path']=" . "'" . $path . "'";
                    $html .= '<script type="text/javascript">' . $addToJs . '</script>';
                    $html = $this->checkAttached($html, $path);
                }
            } else if ($block instanceof Mage_Adminhtml_Block_Widget_Grid) {
                $request = Mage::app()->getRequest();
                $path = $request->getParam('ampinbar_path');
                if ($path) {
                    $this->addParams($block);
                    $html .= Mage::app()->getLayout()->createBlock('ampinbar/adminhtml_params')->toHtml();
                    $html = $this->checkAttached($html, $path);
                }
            }
            $transport->setHtml($html);
        }
    }

    protected function addParams($block) {
        $params = array(
            $block->getId() => array(
                $block->getVarNameLimit() => $block->getParam($block->getVarNameLimit()),
                $block->getVarNamePage() => $block->getParam($block->getVarNamePage()),
                $block->getVarNameSort() => $block->getParam($block->getVarNameSort()),
                $block->getVarNameDir() => $block->getParam($block->getVarNameDir()),
                $block->getVarNameFilter() => $block->getParam($block->getVarNameFilter())
            )
        );
        Mage::getSingleton('ampinbar/params')->setParams($params);
    }

    protected function checkAttached($html, $path) {
        if (Mage::helper('ampinbar')->pinAlreadyAttached($path)) {
            $html .= '<script type="text/javascript">$(\'ampinbar-pin\').addClassName("attached");</script>';
        } else {
            $html .= '<script type="text/javascript">$(\'ampinbar-pin\').removeClassName("attached");</script>';
        }
        return $html;
    }

    public function insertBar($observer) {
        if(Mage::helper('ampinbar')->moduleEnabled()) {
            $block = $observer->getBlock();
            if ($block instanceof Mage_Adminhtml_Block_Page_Header)
            {
                $transport = $observer->getTransport();
                $html = $transport->getHtml() .
                    Mage::app()->getLayout()->createBlock('ampinbar/adminhtml_bar')->toHtml();
                $transport->setHtml($html);
            }
        }
    }
}
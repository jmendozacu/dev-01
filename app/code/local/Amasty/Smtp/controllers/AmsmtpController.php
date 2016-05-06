<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
class Amasty_Smtp_AmsmtpController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/tools/amsmtp_log')
            ->_addBreadcrumb(Mage::helper('amsmtp')->__('System'), Mage::helper('amsmtp')->__('System'))
            ->_addBreadcrumb(Mage::helper('amsmtp')->__('Tools'),  Mage::helper('amsmtp')->__('Tools'))
        ;
        return $this;
    }

    public function logAction()
    {
        $this->_title($this->__('SMTP Email'))
             ->_title($this->__('Sent-out Log'));

        $this->_initAction();
        $this->renderLayout();
    }

    public function debugAction()
    {
        $this->_title($this->__('SMTP Email'))
            ->_title($this->__('Debug Log'));

        $this->_initAction();
        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->_title($this->__('SMTP Email'))
            ->_title($this->__('Sent-out Log'));

        $logId = Mage::app()->getRequest()->getParam('log_id');
        $log = Mage::getModel('amsmtp/log')->load($logId);
        if (!$log->getId())
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amsmtp')->__('The record does not exist.'));
            $this->_redirect('*/*/log');
        } else
        {
            Mage::register('amsmtp_log', $log);
            $this->_initAction();
            $this->renderLayout();
        }
    }

    public function checkAction()
    {
        $result = 0;

        $params = Mage::app()->getRequest()->getParams();

        $transportFacade = Mage::getModel('amsmtp/transport');
        if ($transportFacade->runTest($params))
        {
            $result = 1;
        }

        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode($result)
        );
    }

    public function cleardebugAction()
    {
        Mage::getModel('amsmtp/debug')->truncate();
        $this->_redirect('*/*/debug');
    }

    public function clearlogAction()
    {
        Mage::getModel('amsmtp/log')->truncate();
        $this->_redirect('*/*/log');
    }
}
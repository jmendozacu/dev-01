<?php

class Magebuzz_Emailcancelorder_Model_Observer
{
    public function invoicedStatusChange($event)
    {

        $order = $event->getOrder();
        $orderStatus = $order->getStatus();
        if ($order->getState() == Mage_Sales_Model_Order::STATE_CANCELED){
            $this->_sendStatusMail($order);
        }

    }

    private  function _sendStatusMail($order)
    {
        $emailTemplate  = Mage::getModel('core/email_template');

        $emailTemplate->loadDefault('custom_order_tpl');
//        $emailTemplate->setTemplateSubject('Your order was holded');

        // Get General email address (Admin->Configuration->General->Store Email Addresses)
        $salesData['email'] = Mage::getStoreConfig('trans_email/ident_general/email');
        $salesData['name'] = Mage::getStoreConfig('trans_email/ident_general/name');

        $emailTemplate->setSenderName($salesData['name']);
        $emailTemplate->setSenderEmail($salesData['email']);

        $emailTemplateVariables['username']  = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
        $emailTemplateVariables['order_id'] = $order->getIncrementId();
        $emailTemplateVariables['store_name'] = $order->getStoreName();
        $emailTemplateVariables['store_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        try{
            $emailTemplate->send($order->getCustomerEmail(), $order->getStoreName(), $emailTemplateVariables);
        }
        catch(Exception $error)
        {
            Mage::getSingleton('core/session')->addError($error->getMessage());
            return false;
        }
    }
}
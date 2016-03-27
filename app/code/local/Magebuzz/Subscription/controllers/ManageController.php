<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
require_once Mage::getModuleDir('controllers', 'Mage_Newsletter') . DS . 'ManageController.php';
class Magebuzz_Subscription_ManageController extends Mage_Newsletter_ManageController {
  public function saveAction() {
		$this->getResponse()->setHeader('Content-type', 'application/json');
    $_response = array();
    try {
        Mage::getSingleton('customer/session')->getCustomer()
        ->setStoreId(Mage::app()->getStore()->getId())
        ->setIsSubscribed((boolean)$this->getRequest()->getParam('is_subscribed', false))
        ->save();
        if ((boolean)$this->getRequest()->getParam('is_subscribed', false)) {
            $_response['success'] = 'saved';
            $_response['label'] = Mage::helper('core')->__("You are currently subscribed to 'General Subscription'.");
						$_response['is_subscribed'] = 0;
        } else {
            $_response['success'] = 'removed';
						$_response['label'] = Mage::helper('core')->__("You are currently not subscribed to any newsletter.");
						$_response['is_subscribed'] = 1;
        }
    }
    catch (Exception $e) {
        $_response['success'] = 'error';
    }
    $this->getResponse()->setBody(json_encode($_response));
	}
}
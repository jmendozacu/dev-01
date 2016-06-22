<?php
/*
* Copyright (c) 2013 www.magebuzz.com 
*/
require_once Mage::getModuleDir('controllers', 'Mage_Newsletter') . DS . 'SubscriberController.php';
class Magebuzz_Subscription_SubscriberController extends Mage_Newsletter_SubscriberController {
  /**
	* New subscription action
	*/
	public function newAction() {
		$this->getResponse()->setHeader('Content-type', 'application/json');
    $_response = array();	
		if ((boolean)$this->getRequest()->getParam('subscriber_email', false)) {
				$session            = Mage::getSingleton('core/session');
				$customerSession    = Mage::getSingleton('customer/session');
				$email              = (string) $this->getRequest()->getParam('subscriber_email', false);
				$html = '';
				try {
					if (!Zend_Validate::is($email, 'EmailAddress')) {
							Mage::throwException($this->__('Please enter a valid email address.'));
					}

					if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && 
							!$customerSession->isLoggedIn()) {
							Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
					}

					/* $ownerId = Mage::getModel('customer/customer')
									->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
									->loadByEmail($email)
									->getId();
					if ($ownerId !== null && $ownerId != $customerSession->getId()) {
							Mage::throwException($this->__('This email address is already assigned to another user.'));
					} */
					
					$subscriberCollection = Mage::getModel('newsletter/subscriber')->getCollection()->addFieldToFilter('subscriber_email', $email);
			
					if ($subscriberCollection->getSize()) {
						$_response['status'] = 'error';
						$_response['message'] = Mage::helper('core')->__('This email address is already assigned to another user.');
					}
					else{
						$status = Mage::getModel('newsletter/subscriber')->subscribe($email);
						if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
							$_response['status'] = 'success';
							$_response['message'] = Mage::helper('core')->__('Confirmation request has been sent.');
						}
						else{
							$_response['status'] = 'success';
							$_response['message'] = Mage::helper('core')->__('Thank you for your subscription.');
						}
					}
				}
				catch (Mage_Core_Exception $e) {
					$_response['status'] = 'error';
					$_response['message'] = Mage::helper('core')->__('There was a problem with the subscription: %s', $e->getMessage());
				}
				catch (Exception $e) {
					$_response['status'] = 'error';
					$_response['message'] = Mage::helper('core')->__('There was a problem with the subscription.');
				}
		}
		// Add custom message
		if(Mage::helper('subscription')->isEnabledCustomMsg()) {
			$customMsg = '';
			if(Mage::helper('subscription')->isUseStaticBlockForCustomMsg()){
				$customMsg .= Mage::helper('subscription')->getPopupStaticBlockContent();
			}else{
				$customMsg .= (string)Mage::getStoreConfig('subscription/general/manual_custom_msg');
			}
			$html = 
			'<div class="block" id="ajaxcart_content_option_product">
				<a title="Close" class="ajaxcart-close" href="javascript:void(0)" onclick="ajaxCart.closeOptionsPopup();"></a>
				<div class="ajaxcart-heading">
					<p class="added-success-message">' 		
					. $_response['message'] .
					'</p>
				</div>
				<div class="ajaxcart-body">'.$customMsg.'</div>
			</div>
			';
		}else{
			$html = 
			'<div class="block" id="ajaxcart_content_option_product">
				<a title="Close" class="ajaxcart-close" href="javascript:void(0)" onclick="ajaxCart.closeOptionsPopup();"></a>
				<div class="ajaxcart-heading">
					<p class="added-success-message">' 		
					. $_response['message'] .
					'</p>
				</div>
			</div>
			';
		}
		$_response['html'] = $html;
		$this->getResponse()->setBody(json_encode($_response));
	}
}
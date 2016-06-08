<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Ajaxlogin
 */
require_once 'Mage/Customer/controllers/AccountController.php';

class Amasty_Ajaxlogin_AccountController extends Mage_Customer_AccountController {

	public function isajaxloginAction(){
        $output = 0;
        if ($this->_getSession()->isLoggedIn()){
            $output = 1;
        }
	    $this->getResponse()->setHeader('Content-type', 'application/json');
	    $this->getResponse()->setBody($output);
	    
	    //$jsonData = json_encode(array('status' => $output));
	    //$this->getResponse()->setHeader('Content-type', 'application/json');
	    //$this->getResponse()->setBody($jsonData);
    }

    /**
	 * Login post action
	 */
	public function loginPostAction()
	{
			if (!$this->_validateFormKey()) {
					$this->_redirect('*/*/');
					return;
			}

			if ($this->_getSession()->isLoggedIn()) {
					$this->_redirect('*/*/');
					return;
			}
			$session = $this->_getSession();

			if ($this->getRequest()->isPost()) {
					$login = $this->getRequest()->getPost('login');
					if (!empty($login['username']) && !empty($login['password'])) {
							try {
								$session->login($login['username'], $login['password']);
								if($session->getCustomer()->getData('am_is_activated') == '2'){
									if ($session->getCustomer()->getIsJustConfirmed()) {
										$this->_welcomeCustomer($session->getCustomer(), true);
									}
								}else{
									$session->addError($this->__('Your account is not approved and cannot log in.'));
								}
							} catch (Mage_Core_Exception $e) {
									switch ($e->getCode()) {
											case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
													$value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
													$message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
													break;
											case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
													$message = $e->getMessage();
													break;
											default:
													$message = $e->getMessage();
									}
									$session->addError($message);
									$session->setUsername($login['username']);
							} catch (Exception $e) {
									// Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
							}
					} else {
							$session->addError($this->__('Login and password are required.'));
					}
			}

			$this->_loginPostRedirect();
	}
	
	public function visitorloginAction() {			
		if ($this->_getSession()->isLoggedIn()) {
			$this->_redirect('*/*/');
			return;
		}
		$this->getResponse()->setHeader('Login-Required', 'true');
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
		$this->renderLayout();
  }
	
	public function rewardpointsAction() {
		
		$this->loadLayout();
		
		$this->_initLayoutMessages('customer/session');
		

		$this->getLayout()->getBlock('head')->setTitle($this->__('Reward Points'));
		$this->renderLayout();
	}
}

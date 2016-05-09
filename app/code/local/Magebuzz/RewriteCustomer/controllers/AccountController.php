<?php
require_once Mage::getModuleDir('controllers', 'Mage_Customer').DS.'AccountController.php';
class Magebuzz_RewriteCustomer_AccountController extends Mage_Customer_AccountController
{
    protected function _getCustomerErrors($customer)
    {
        $errors = array();
        $request = $this->getRequest();
        if ($request->getPost('create_address')) {
            $errors = $this->_getErrorsOnCustomerAddress($customer);
        }
        $customerForm = $this->_getCustomerForm($customer);
        $customerData = $customerForm->extractData($request);

        $customerForm->compactData($customerData);
        $customer->setPassword($request->getPost('password'));
        $customer->setPasswordConfirmation($request->getPost('confirmation'));

        return $errors;
    }
}
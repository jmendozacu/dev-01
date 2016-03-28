<?php
require_once(Mage::getModuleDir('controllers','Mage_Customer').DS.'AddressController.php');
class Magebuzz_Customaddress_AddressController extends Mage_Customer_AddressController{
  public function setDefaultAction(){
    $this->getResponse()->setHeader('Content-type', 'application/json');
    $_response = array();
    $_response['success'] = 'error';
    
    $actionSave = $this->getRequest()->getParam('action');
    $addressId = $this->getRequest()->getParam('address_id');
    
    $customer = $this->_getSession()->getCustomer();
    $address  = Mage::getModel('customer/address');
    
    if ($addressId) {
      $existsAddress = $customer->getAddressById($addressId);
      if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
        $address->setId($existsAddress->getId());
        $address->setCustomerId($customer->getId());
        
        if($actionSave == 'shipping'){
          $oldAddressId = $customer->getDefaultShipping();
          $address->setIsDefaultShipping(1);
        }
        if($actionSave == 'billing'){
          $oldAddressId = $customer->getDefaultBilling();
          $address->setIsDefaultBilling(1);
        }
        try{
          $address->save();
          $_response['success'] = 'saved';
          $_response['old_id'] = $oldAddressId;
        }
        catch(Exception $e){
          $_response['susccess'] == 'error';
        }
      }
    }
    
    $this->getResponse()->setBody(json_encode($_response));
  }
}

<?php
class Magebuzz_Importcustomer_Adminhtml_ImportcustomerController extends Mage_Adminhtml_Controller_Action{
  public function indexAction(){
    $filepath = Mage::getBaseDir('media') . DS . 'importcustomer' .DS. 'customer.csv';
    $helper = Mage::helper('importcustomer');
    $helper->insertCustomer();
    
    $message = $this->__('Import Successfully');
    Mage::getSingleton('core/session')->addSuccess($message);
    $this->_redirect('adminhtml/system_config/edit/section/importcustomer');
  }
}

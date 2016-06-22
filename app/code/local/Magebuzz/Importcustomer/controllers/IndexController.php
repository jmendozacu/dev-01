<?php
class Magebuzz_Importcustomer_IndexController extends Mage_Core_Controller_Front_Action{
  public function testAction(){
    $helper = Mage::helper('importcustomer');
    $helper->insertCustomer();
  }
}
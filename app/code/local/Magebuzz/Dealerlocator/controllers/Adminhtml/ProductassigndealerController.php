<?php
class Magebuzz_Dealerlocator_Adminhtml_ProductassigndealerController extends Mage_Adminhtml_Controller_Action {
  public function indexAction() {
    $this->loadLayout();
    $this->getLayout()->getBlock('product.edit.tabs.dealer')->setDealers($this->getRequest()->getPost('oblock', null));
    $this->renderLayout();
  }
  
  public function gridAction(){
    $this->loadLayout();
    $this->getLayout()->getBlock('product.edit.tabs.dealer')->setDealers($this->getRequest()->getPost('oblock', null));
    $this->renderLayout();
  }
}
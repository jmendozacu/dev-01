<?php
class Mageplace_Flippingbook_IndexController extends Mage_Core_Controller_Front_Action
{
  public function indexAction()
  {
    $this->loadLayout();
    $head = $this->getLayout()->getBlock('head');
    $head->setTitle($this->__('Index living mall Catalog'));
    $this->renderLayout();
  }
}
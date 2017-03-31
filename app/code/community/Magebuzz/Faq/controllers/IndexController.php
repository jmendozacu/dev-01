<?php

/**
 * @copyright   Copyright (c) 2013 AZeBiz Co. LTD
 */
class Magebuzz_Faq_IndexController extends Mage_Core_Controller_Front_Action
{
  public function searchAction()
  {
    $this->indexAction();
  }

  public function indexAction()
  {
    $header = Mage::helper('faq')->getHeaderPage();
    if (!$header) {
      $header = "FAQs";
    }
    $this->loadLayout();
    $this->getLayout()->getBlock('head')->setTitle($header);
    $this->renderLayout();
  }

  public function viewAction()
  {
    $this->loadLayout();
    $this->getLayout()->getBlock('head')->setTitle(Mage::helper('faq')->__('FAQ Category'));
    $this->renderLayout();
  }

  public function detailAction()
  {
    $this->loadLayout();
    $this->getLayout()->getBlock('head')->setTitle(Mage::helper('faq')->__('FAQ Detail'));
    $this->renderLayout();
  }
}
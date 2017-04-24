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
  public function getFaqsAction(){

    $this->getResponse()->setHeader('Content-type', 'application/json');
    $response = array();
    $category_id = $this->getRequest()->getParam('category_id');
    $category_title = Mage::getModel('faq/category')->load($category_id)->getData('category_name');
    $collection = Mage::getModel('faq/faq')->getAllFaqs($category_id)->addFieldToFilter('is_active',1);
    if($collection->getSize()) {
      $response['result'] = Mage::app()->getLayout()->createBlock('faq/faq')
        ->setCollection($collection)
        ->setTemplate('faq/list_faq.phtml')->toHTML();
      $response['success'] = 'true';
    } else {
      $response['no_result'] = '<div class="list-faqs list-faq-'.$category_id.'"><p class="category_title_faq">'.Mage::helper('faq')->__($category_title).'</p><p>'.Mage::helper('faq')->__('There are no FAQ yet.').'</p></div>';
      $response['success'] = 'false';
    }
    $this->getResponse()->setBody(json_encode($response));
    return;
  }
}
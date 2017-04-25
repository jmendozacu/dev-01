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

  public function getListBooksByCategoryAction()
  {
    $this->getResponse()->setHeader('Content-type', 'application/json');
    $_response = array();
    $category_id = $this->getRequest()->getParam('category_id');
    if($category_id == 'all'){
      $collection = Mage::getModel('flippingbook/magazine')->getCollection();
    }else{
      $collection = Mage::getModel('flippingbook/magazine')->getCollection()
        ->addFieldToFilter('magazine_category_id', $category_id);
    }
    if($collection->getData()){
      $html_list = Mage::app()->getLayout()->createBlock('flippingbook/frontend_listbooks')
//        ->setData('area','frontend')
        ->setCollection($collection)
        ->setCategoryId($category_id)
        ->setTemplate('flippingbook/magazine/list_books_json.phtml')->toHtml();
      $success = true;
    }else{
      $html_list = '';
      $success = false;
    }
    $_response['html'] = $html_list;
    $_response['success'] = $success;
    $this->getResponse()->setBody(json_encode($_response));
    return;
  }
}
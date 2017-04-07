<?php

class Magebuzz_Faq_Block_Faq extends Mage_Core_Block_Template
{
  public function __construct()
  {
    parent::__construct();
    $collection = Mage::getModel('faq/faq')->getCollection()
      ->addFieldToFilter('is_active', 1)
      ->setOrder('sort_order_faq', 'ASC');
    $this->setCollection($collection);
    }

  protected function _prepareLayout()
  {
    if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
      $breadcrumbsBlock->addCrumb('home', array(
        'label' => Mage::helper('catalog')->__('Home'),
        'title' => Mage::helper('catalog')->__('Go to Home Page'),
        'link' => Mage::getBaseUrl()
      ));

      $breadcrumbsBlock->addCrumb('faq', array(
        'label' => Mage::helper('faq')->__('FAQ'),
        'title' => Mage::helper('faq')->__('FAQ'),
      ));
    }
      parent::_prepareLayout();

    $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
    $pager->setAvailableLimit(array(10=>10,20=>20,30=>30,'all'=>'all'));
    $pager->setCollection($this->getCollection());
    $this->setChild('pager', $pager);
    $this->getCollection()->load();
    return $this;
  }

  public function getPagerHtml()
  {
    return $this->getChildHtml('pager');
  }
  public function getQuestion($category)
  {
    $questions = Mage::getModel('faq/faq')->getResource()->getQuestion($category);
    return $questions;
  }

  public function getAllQuestion()
  {
    $allQuestion = Mage::getModel('faq/faq')->getResource()->getAllQuestion();
    return $allQuestion;
  }

  public function getSearchResult()
  {
    $categoriesIds = $this->getAllAvaliableCategories();
    $result = array();
    foreach ($categoriesIds as $catId) {
      $catData = $this->getFaqsData($catId);
      foreach ($catData as $data) {
        $result[] = $data;
      }
    }
    return $result;
  }

  public function getAllAvaliableCategories()
  {
    $collection = $this->getCategories();
    $categoriesIds = array();
    foreach ($collection as $item) {
      $categoriesIds[] = $item->getCategoryId();
    }
    return $categoriesIds;
  }

  public function getCategories()
  {
    $sortOrder = Mage::getStoreConfig('faq/general/sort_order');
    $storeIds = array(Mage::app()->getStore()->getId(), Mage_Core_Model_App::ADMIN_STORE_ID);
    $collection = Mage::getModel('faq/category')->getCollection();
    $collection->addFieldToFilter('is_active',array("neq"=>'0'));
    $collection->getSelect()
      ->join(array('fstore' => Mage::getModel('core/resource')->getTableName('faq_category_store')), 'main_table.category_id=fstore.category_id')
      ->where('fstore.store_id IN (?)', $storeIds);
//        var_dump($collection->getData());die();
    $collection->getSelect()->group('main_table.category_id');
    $collection->setOrder('sort_order', $sortOrder);
    return $collection;

  }

  public function getFaqsData($cat_id)
  {
    $sortOrder = Mage::getStoreConfig('faq/general/sort_order');
    $storeIds = array(Mage::app()->getStore()->getId(), Mage_Core_Model_App::ADMIN_STORE_ID);
    $collection = Mage::getModel('faq/faq')->getCollection();
    $collection->addFieldToFilter('is_active',array("neq"=>'0'));
    $collection->getSelect()
      ->join(array('faq_category' => Mage::getModel('core/resource')->getTableName('faq_category_item')), 'main_table.faq_id=faq_category.faq_id')
      ->join(array('fstore' => Mage::getModel('core/resource')->getTableName('faq_store')), 'main_table.faq_id=fstore.faq_id')
      ->where('faq_category.category_id=?', $cat_id)
      ->where('fstore.store_id IN (?)', $storeIds)->group('main_table.faq_id');
    $collection->setOrder('sort_order_faq', $sortOrder);
    return $collection;
  }

  public function getCategoryTitle()
  {
    $cat_id = $this->getRequest()->getParam('cid');
    $title = Mage::getModel('faq/category')->getTitleFaq($cat_id);
    if ($title == null || $cat_id == null)
      return null;
    return $title;
  }

  public function getCategoryUrl($id)
  {
    $category = Mage::getModel('faq/category')->load($id);
    $url_key = $category->getUrlKey();
    return $this->getUrl('faq/category/' . $url_key, array());
  }

  public function getQuestionUrl($id)
  {
    $question = Mage::getModel('faq/faq')->load($id);
    $url_key = $question->getUrlKey();
    return $this->getUrl('faq/question/' . $url_key, array());
  }

}
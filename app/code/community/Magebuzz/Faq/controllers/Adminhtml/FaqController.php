<?php

/**
 * @copyright   Copyright (c) 2013 AZeBiz Co. LTD
 */
class Magebuzz_Faq_Adminhtml_FaqController extends Mage_Adminhtml_Controller_action
{
  public function indexAction()
  {
    $this->_initAction()
      ->renderLayout();
  }

  protected function _initAction()
  {
    $this->loadLayout()
      ->_setActiveMenu('magebuzz/faq')
      ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
    if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
      $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
    }
    return $this;
  }

  public function editAction()
  {
    $id = $this->getRequest()->getParam('id');
    $model = Mage::getModel('faq/faq')->load($id);

    if ($model->getId() || $id == 0) {
      $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
      if (!empty($data)) {
        $model->setData($data);
      }

      Mage::register('faq_data', $model);

      $this->loadLayout();
      $this->_setActiveMenu('magebuzz/faq');

      $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
      $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

      $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

      $this->_addContent($this->getLayout()->createBlock('faq/adminhtml_faq_edit'))
        ->_addLeft($this->getLayout()->createBlock('faq/adminhtml_faq_edit_tabs'));

      $this->renderLayout();
    } else {
      Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('Item does not exist'));
      $this->_redirect('*/*/');
    }
  }

  public function newAction()
  {
    $this->_forward('edit');
  }

  public function saveAction()
  {
    $idOld = $this->getRequest()->getParam('id');
    $data = $this->getRequest()->getPost();
    $id = Mage::helper('faq')->getIdbyQuestion($data['question']);
    if (!isset($idOld)) {
      if ($data['question'] == Mage::getModel('faq/faq')->load($id)->getData('question')) {
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('This name \''.$data["question"] .'\' is exists.'));
        Mage::getSingleton('adminhtml/session')->setFormData($data);
        $this->_redirect('*/*/new');
        return;
      }
    }
    $model = Mage::getModel('faq/faq');
    $model->setData($data)
      ->setId($this->getRequest()->getParam('id'));
    try {
      if (!isset($data['url_key']) || $data['url_key'] == '') {
        $data['url_key'] = $data['question'];
      }
      $data['url_key'] = Mage::helper('faq')->generateUrl($data['url_key']);

      $model->setData($data)
        ->setId($this->getRequest()->getParam('id'));

      if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
        $model->setCreatedTime(now())
          ->setUpdateTime(now());
      } else {
        $model->setUpdateTime(now());
      }
      /* remove rewrite url*/
//      $rewriteModel = Mage::getModel('core/url_rewrite');
//      $id_path = 'faq/question/' . $model->getId();
//      $rewriteModel->loadByIdPath($id_path);
//      if (!$rewriteModel->getData('request_path')) {
//        $rewriteModel->setData('id_path', 'faq/question/' . $model->getId());
//        $rewriteModel->setData('request_path', 'faq/question/' . $data['url_key']);
//        $rewriteModel->setData('target_path', 'faq/category/detail/id/' . $model->getId());
//        $rewriteModel->save();
//      }


      //$url_key = Mage::helper('faq')->generateUrl($model->getQuestion());
      $model->save();

      //end rewrite url
      Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('faq')->__('FAQ was successfully saved'));
      Mage::getSingleton('adminhtml/session')->setFormData(false);

      if ($this->getRequest()->getParam('back')) {
        $this->_redirect('*/*/edit', array('id' => $model->getId()));
        return;
      }

      $this->_redirect('*/*/');
      return;

    } catch (Exception $e) {
      Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      Mage::getSingleton('adminhtml/session')->setFormData($data);
      $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
      return;
    }

    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('Unable to find FAQ to save'));
    $this->_redirect('*/*/');
  }

  public function deleteAction()
  {
    if ($this->getRequest()->getParam('id') > 0) {
      try {
        $model = Mage::getModel('faq/faq');
        //delete rewrite url
        $faqId = $this->getRequest()->getParam('id');
        $model->load($faqId);

//        $question = Mage::helper('faq')->generateUrl($model->getQuestion());

        //remove del rewirte url
//        $rewriteModel = Mage::getModel('core/url_rewrite');
//        $request_path = 'faq/question/' . $question;
//        $rewriteModel->loadByRequestPath($request_path);
//        if ($rewriteModel->getId()) {
//          $rewriteModel->delete();
//        }
        //delete faq
        $model->setId($this->getRequest()->getParam('id'))
          ->delete();

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('FAQ was successfully deleted'));
        $this->_redirect('*/*/');
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
      }
    }
    $this->_redirect('*/*/');
  }

  public function massDeleteAction()
  {
    $faqIds = $this->getRequest()->getParam('faq');
    if (!is_array($faqIds)) {
      Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
    } else {
      try {
        foreach ($faqIds as $faqId) {
          //remove del url rewirte
//          $model = Mage::getModel('faq/faq')->load($faqId);
//          $question = Mage::helper('faq')->generateUrl($model->getQuestion());
//          $rewriteModel = Mage::getModel('core/url_rewrite');
//          $request_path = 'faq/question/' . $question;
//          $rewriteModel->loadByRequestPath($request_path);
//          if ($rewriteModel->getId()) {
//            $rewriteModel->delete();
//          }
          $faq = Mage::getModel('faq/faq')->load($faqId);
          $faq->delete();
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(
          Mage::helper('adminhtml')->__(
            'Total of %d record(s) were successfully deleted', count($faqIds)
          )
        );
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }
    }
    $this->_redirect('*/*/index');
  }

  public function massStatusAction()
  {
    $faqIds = $this->getRequest()->getParam('faq');
    if (!is_array($faqIds)) {
      Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
    } else {
      try {
        foreach ($faqIds as $faqId) {
          $oldStores = Mage::getModel('faq/faq')
            ->load($faqId)->getResource()->lookupStoreIds($faqId);
          $faq = Mage::getModel('faq/faq')
            ->load($faqId)
            ->setIsActive($this->getRequest()->getParam('status'))
            ->setIsMassupdate(true)
            ->setStores($oldStores)
            ->save();
        }
        $this->_getSession()->addSuccess(
          $this->__('Total of %d record(s) were successfully updated', count($faqIds))
        );
      } catch (Exception $e) {
        $this->_getSession()->addError($e->getMessage());
      }
    }
    $this->_redirect('*/*/index');
  }

  public function validateFAQ()
  {

  }

  protected function _isAllowed()
  {
    return Mage::getSingleton('admin/session')->isAllowed('magebuzz/faq');
  }
}
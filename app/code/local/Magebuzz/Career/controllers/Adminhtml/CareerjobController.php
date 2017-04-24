<?php
class Magebuzz_Career_Adminhtml_CareerjobController extends Mage_Adminhtml_Controller_action{
  protected function _initAction() {
    $this->loadLayout()->_setActiveMenu('career/items')->_addBreadcrumb(Mage::helper('adminhtml')->__('Job Manager'), Mage::helper('adminhtml')->__('Job Manager'));

    return $this;
  }

  public function indexAction() {
    $this->_initAction()->renderLayout();
  }

  public function editAction() {
    $id = $this->getRequest()->getParam('id');
    $model = Mage::getModel('career/job')->load($id);

    if ($model->getId() || $id == 0) {
      $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
      if (!empty($data)) {
        $model->setData($data);
      }

      Mage::register('job_current_submit', $model);

      $this->loadLayout();
      $this->_setActiveMenu('career/job');

      $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
      $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

      $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

      $this->_addContent($this->getLayout()->createBlock('career/adminhtml_job_edit'))
        ->_addLeft($this->getLayout()->createBlock('career/adminhtml_job_edit_tabs'));

      $this->renderLayout();
    } else {
      Mage::getSingleton('adminhtml/session')->addError(Mage::helper('career')->__('Item does not exist'));
      $this->_redirect('*/*/');
    }
  }

  public function newAction() {
    $this->_forward('edit');
  }

  public function gridAction() {
    $this->getResponse()->setBody($this->getLayout()->createBlock('career/adminhtml_job_grid')->toHtml());
    return;
  }

  public function saveAction() {
    if ($data = $this->getRequest()->getPost()) {
      $model = Mage::getModel('career/job');
      $id = $this->getRequest()->getParam('id');
      $model->setData($data)->setId($id);
      try {
        $model->save();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('career')->__('Job has save successfully'));
        $this->_redirect('*/*/');
        return;
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        $this->_redirect('*/*/');
        return;
      }
    }
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('career')->__('Unable to change save job'));
    $this->_redirect('*/*/');
  }

  public function deleteAction() {
    if ($this->getRequest()->getParam('id') > 0) {
      try {
        $model = Mage::getModel('career/job');
        $model->setId($this->getRequest()->getParam('id'))->delete();

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Data was successfully deleted'));
        $this->_redirect('*/*/');
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
      }
    }
    $this->_redirect('*/*/');
  }

  public function massDeleteAction() {
    $formIds = $this->getRequest()->getParam('job');
    if (!is_array($formIds)) {
      Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
    } else {
      try {
        foreach ($formIds as $formId) {
          $formData = Mage::getModel('career/job')->load($formId);
          $formData->delete();
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($formIds)));
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }
    }
    $this->_redirect('*/*/index');
  }

  public function massStatusAction() {
    $jobIds = $this->getRequest()->getParam('job');
    if (!is_array($jobIds)) {
      Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
    } else {
      try {
        foreach ($jobIds as $jobId) {
          $bannerads = Mage::getSingleton('career/job')->load($jobId)->setStatus($this->getRequest()->getParam('status'))->setIsMassupdate(TRUE)->save();
        }
        $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully updated', count($jobId)));
      } catch (Exception $e) {
        $this->_getSession()->addError($e->getMessage());
      }
    }
    $this->_redirect('*/*/index');
  }

  protected function _isAllowed()	{
    return Mage::getSingleton('admin/session')->isAllowed('career/items');
  }
}
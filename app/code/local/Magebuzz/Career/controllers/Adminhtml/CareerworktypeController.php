<?php
class Magebuzz_Career_Adminhtml_CareerworktypeController extends Mage_Adminhtml_Controller_action{
  protected function _initAction() {
    $this->loadLayout()->_setActiveMenu('career/items')->_addBreadcrumb(Mage::helper('adminhtml')->__('Work Type Manager'), Mage::helper('adminhtml')->__('Work Type Manager'));

    return $this;
  }

  public function indexAction() {
    $this->_initAction()->renderLayout();
  }

  public function editAction() {
    $id = $this->getRequest()->getParam('id');
    $model = Mage::getModel('career/worktype')->load($id);

    if ($model->getId() || $id == 0) {
      $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
      if (!empty($data)) {
        $model->setData($data);
      }

      Mage::register('worktype_current_submit', $model);

      $this->loadLayout();
      $this->_setActiveMenu('career/worktype');

      $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
      $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

      $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

      $this->_addContent($this->getLayout()->createBlock('career/adminhtml_worktype_edit'))
        ->_addLeft($this->getLayout()->createBlock('career/adminhtml_worktype_edit_tabs'));

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
    $this->getResponse()->setBody($this->getLayout()->createBlock('career/adminhtml_worktype_grid')->toHtml());
    return;
  }

  public function saveAction() {
    if ($data = $this->getRequest()->getPost()) {
      $model = Mage::getModel('career/worktype');
      $id = $this->getRequest()->getParam('id');
      $model->setData($data)->setId($id);
      try {
        $model->save();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('career')->__('Work Type has save successfully'));
        $this->_redirect('*/*/');
        return;
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        $this->_redirect('*/*/');
        return;
      }
    }
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('career')->__('Unable to change save work type'));
    $this->_redirect('*/*/');
  }

  public function deleteAction() {
    if ($this->getRequest()->getParam('id') > 0) {
      try {
        $model = Mage::getModel('career/worktype');
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
    $formIds = $this->getRequest()->getParam('worktype');
    if (!is_array($formIds)) {
      Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
    } else {
      try {
        foreach ($formIds as $formId) {
          $formData = Mage::getModel('career/worktype')->load($formId);
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
    $worktypeIds = $this->getRequest()->getParam('worktype');
    if (!is_array($worktypeIds)) {
      Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
    } else {
      try {
        foreach ($worktypeIds as $worktypeId) {
          $bannerads = Mage::getSingleton('career/worktype')->load($worktypeId)->setStatus($this->getRequest()->getParam('status'))->setIsMassupdate(TRUE)->save();
        }
        $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully updated', count($worktypeId)));
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
<?php
class Magebuzz_Franchise_Adminhtml_FranchiseController extends Mage_Adminhtml_Controller_action {
    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('franchise/items')->_addBreadcrumb(Mage::helper('adminhtml')->__('Application Manager'), Mage::helper('adminhtml')->__('Application Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()->renderLayout();
    }
    public function viewAction()
    {
        $this->_initAction();

        $submit = Mage::getModel('franchise/franchise');

        $id  = $this->getRequest()->getParam('id');
        $submit->load($id);

        if (!$submit->getId()) {
            $this->_getSession()->addError($this->__('This submit is no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        Mage::register('franchise_current_submit', $submit);

        $this->renderLayout();
    }


    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('franchise/franchise');
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
        $formIds = $this->getRequest()->getParam('franchise');
        if (!is_array($formIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($formIds as $formId) {
                    $formData = Mage::getModel('franchise/franchise')->load($formId);
                    $formData->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($formIds)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
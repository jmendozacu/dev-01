<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Adminhtml_Flippingbook_ResolutionController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->_usedModuleName = 'flippingbook';

        $this->loadLayout()
            ->_setActiveMenu('flippingbook/resolution')
            ->_title($this->__('HTML5 Flipping Book'))
            ->_addBreadcrumb($this->__('HTML5 Flipping Book'), $this->__('HTML5 Flipping Book'));

        return $this;
    }


    public function indexAction()
    {
        if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_initAction()
            ->_title($this->__('Manage Resolutions'))
            ->_addContent($this->getLayout()->createBlock('flippingbook/adminhtml_resolution'))
            ->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('flippingbook/adminhtml_resolution_grid')->toHtml()
        );
    }


    public function newAction()
    {
        $this->_forward('edit');
    }


    public function editAction()
    {
        $model = Mage::getModel('flippingbook/resolution');

        $id = $this->getRequest()->getParam('resolution_id');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->_getSession()->addError($this->__('This resolution does not exist'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('flippingbook_resolution', $model);

        $title = $id ? $this->__('Edit Resolution') : $this->__('New Resolution');
        $this->_initAction()
            ->_title($title)
            ->_addBreadcrumb($title, $title)
            ->_addContent($this->getLayout()->createBlock('flippingbook/adminhtml_resolution_edit'))
            ->renderLayout();
    }

    /**
     * Action that does the actual saving process and redirects back to overview
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('flippingbook/resolution');
            $model->setData($data);

            try {
                $model->save();

                $this->_getSession()->addSuccess($this->__('Resolution was successfully saved'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('resolution_id' => $model->getId()));
                    return;
                }
            } catch (Exception $e) {
                $this->_getSession()->addException($e, $e->getMessage());
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('resolution_id' => $this->getRequest()->getParam('resolution_id')));
                return;
            }
        }

        $this->_redirect('*/*/');
    }


    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('resolution_id')) {
            try {
                $model = Mage::getModel('flippingbook/resolution');
                $model->load($id);
                $model->delete();

                $this->_getSession()->addSuccess($this->__('Resolution was successfully deleted'));
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('resolution_id' => $id));
                return;
            }
        }

        $this->_getSession()->addError($this->__('Unable to find a Resolution to delete'));

        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('resolutiontable');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Please select items.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $item = Mage::getModel('flippingbook/resolution')->load($id);
                    $item->delete();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted', count($ids))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

}
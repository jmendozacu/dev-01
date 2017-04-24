<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Adminhtml_Flippingbook_CategoryController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->_usedModuleName = 'flippingbook';

        $this->loadLayout()
            ->_setActiveMenu('flippingbook/category')
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
            ->_title($this->__('Manage Categories'))
            ->_addContent($this->getLayout()->createBlock('flippingbook/adminhtml_category'))
            ->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('flippingbook/adminhtml_category_grid')->toHtml()
        );
    }


    public function newAction()
    {
        $this->_forward('edit');
    }


    public function editAction()
    {
        $model = Mage::getModel('flippingbook/category');

        $id = $this->getRequest()->getParam('category_id');
        if($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->_getSession()->addError($this->__('This category does not exist'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if(!empty($data)) {
            $model->setData($data);
        }

        Mage::register('flippingbook_category', $model);

        $title = $id ? $this->__('Edit Category') : $this->__('New Category');
        $this->_initAction()
            ->_title($title)
            ->_addBreadcrumb($title, $title)
            ->_addContent($this->getLayout()->createBlock('flippingbook/adminhtml_category_edit'))
            ->renderLayout();
    }


    public function saveAction()
    {
        if($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('flippingbook/category');
            $model->setData($data);

            try {
                $model->save();

                $this->_getSession()->addSuccess($this->__('Category was successfully saved'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array ('category_id' => $model->getId()));
                    return;
                }
            } catch (Exception $e) {
                $this->_getSession()->addException($e, $e->getMessage());
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array ('category_id' => $this->getRequest()->getParam('category_id')));
                return;
            }
        }

        $this->_redirect('*/*/');
    }


    public function deleteAction()
    {
        if($id = $this->getRequest()->getParam('category_id')) {
            try {
                $model = Mage::getModel('flippingbook/category');
                $model->load($id);
                $model->delete();

                $this->_getSession()->addSuccess($this->__('Category was successfully deleted'));
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array ('category_id' => $id));
                return;
            }
        }

        $this->_getSession()->addError($this->__('Unable to find a Category to delete'));

        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('categorytable');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Please select items.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $item = Mage::getModel('flippingbook/category')->load($id);
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
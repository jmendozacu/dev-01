<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Adminhtml_Flippingbook_PageController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->_usedModuleName = 'flippingbook';

        $this->loadLayout()
            ->_setActiveMenu('flippingbook/page')
            ->_title($this->__('HTML5 Flipping Book'))
            ->_addBreadcrumb($this->__('HTML5 Flipping Bookk'), $this->__('HTML5 Flipping Book'));

        return $this;
    }


    public function indexAction()
    {
        if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_initAction()
            ->_title($this->__('Manage Pages'))
            ->_addContent($this->getLayout()->createBlock('flippingbook/adminhtml_page'))
            ->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('flippingbook/adminhtml_page_grid')->toHtml()
        );
    }


    public function newAction()
    {
        $this->_forward('edit');
    }


    public function editAction()
    {
        $model          = Mage::getModel('flippingbook/page');
        $magazine_model = Mage::getModel('flippingbook/magazine');

        $id = $this->getRequest()->getParam('page_id');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->_getSession()->addError($this->__('This page does not exist'));
                $this->_redirect('*/*/');
                return;
            }

            $magazine_model->load($model->getPageMagazineId());
        }

        Mage::register('flippingbook_page', $model);
        Mage::register('flippingbook_magazine', $magazine_model);

        $title = $id ? $this->__('Edit Page') : $this->__('New Page');
        $this->_initAction()
            ->_title($title)
            ->_addBreadcrumb($title, $title)
            ->_addContent($this->getLayout()->createBlock('flippingbook/adminhtml_page_edit'))
            ->renderLayout();
    }


    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('flippingbook/page');
            $model->setData($data);

            try {
                $model->save();

                $this->_getSession()->addSuccess($this->__('Page was successfully saved'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('page_id' => $model->getId()));
                    return;
                }
            } catch (Exception $e) {
                $this->_getSession()->addException($e, $e->getMessage());
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('page_id' => $this->getRequest()->getParam('page_id')));
                return;
            }
        }

        $this->_redirect('*/*/');
    }

    public function enableAction()
    {
        if ($id = $this->getRequest()->getParam('page_id')) {
            try {
                $model = Mage::getModel('flippingbook/page');
                $model->load($id);
                $model->setIsActive(!$model->getIsActive());
                $model->save();

                $this->_getSession()->addSuccess($this->__('Page was successfully enabled/disabled'));
                $this->_redirect('*/*/index');
                return;

            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('page_id' => $id));
                return;
            }
        }

        $this->_getSession()->addError($this->__('Page to find a Book to enable/disable'));

        $this->_redirect('*/*/');
    }

    public function massEnableAction()
    {
        $ids = $this->getRequest()->getParam('pagetable');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Please select items.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('flippingbook/page');
                    $model->load($id);
                    $model->setIsActive(!$model->getIsActive());
                    $model->save();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('flippingbook')->__('Total of %d record(s) were enabled/disabled', count($ids))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }


    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('page_id')) {
            try {
                $model = Mage::getModel('flippingbook/page');
                $model->load($id);
                $model->delete();

                $this->_getSession()->addSuccess($this->__('Page was successfully deleted'));
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('page_id' => $id));
                return;
            }
        }

        $this->_getSession()->addError($this->__('Unable to find a Page to delete'));

        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('pagetable');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Please select items.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $item = Mage::getModel('flippingbook/page')->load($id);
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

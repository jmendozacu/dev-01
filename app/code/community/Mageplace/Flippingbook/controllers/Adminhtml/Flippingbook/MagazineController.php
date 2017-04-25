<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Adminhtml_Flippingbook_MagazineController  extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->_usedModuleName = 'flippingbook';

        $this->loadLayout()
            ->_setActiveMenu('flippingbook/magazine')
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
            ->_title($this->__('Manage Books'))
            ->_addContent($this->getLayout()->createBlock('flippingbook/adminhtml_magazine'))
            ->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('flippingbook/adminhtml_magazine_grid')->toHtml()
        );
    }


    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $model = Mage::getModel('flippingbook/magazine');

        $id = $this->getRequest()->getParam('magazine_id');
        if($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->_getSession()->addError($this->__('This book does not exist'));
                $this->_redirect('*/*/index');
                return;
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if(!empty($data)) {
            $data['magazine_thumbnail']				= $model->getData('magazine_thumbnail');
            $data['magazine_background_pdf']	= $model->getData('magazine_background_pdf');
            $model->setData($data);
        }

        Mage::register('flippingbook_magazine', $model);

        $title = $id ? $this->__('Edit Book') : $this->__('New Book');
        $this->_initAction()
            ->_title($title)
            ->_addBreadcrumb($title, $title)
            ->_addContent($this->getLayout()->createBlock('flippingbook/adminhtml_magazine_edit'))
            ->_addLeft($this->getLayout()->createBlock('flippingbook/adminhtml_magazine_edit_tabs'))
            ->renderLayout();
    }


    public function saveAction()
    {
        if($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('flippingbook/magazine');
            $model->setData($data);

            try {
                $model->save();

                $this->_getSession()->addSuccess($this->__('Book was successfully saved'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array ('magazine_id' => $model->getId()));
                    return;
                }
            } catch (Exception $e) {
                $this->_getSession()->addException($e, $e->getMessage());
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array ('magazine_id' => $this->getRequest()->getParam('magazine_id')));
                return;
            }
        }

        $this->_redirect('*/*/index');
    }

    public function enableAction()
    {
        if($id = $this->getRequest()->getParam('magazine_id')) {
            try {
                $model = Mage::getModel('flippingbook/magazine');
                $model->load($id);
                $model->setIsActive(!$model->getIsActive());
                $model->save();

                $this->_getSession()->addSuccess($this->__('Book was successfully enabled/disabled'));
                $this->_redirect('*/*/index');
                return;

            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array ('magazine_id' => $id));
                return;
            }
        }

        $this->_getSession()->addError($this->__('Unable to find a Book to enable/disable'));

        $this->_redirect('*/*/');
    }

    public function massEnableAction()
    {
        $ids = $this->getRequest()->getParam('magazinetable');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Please select items.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('flippingbook/magazine');
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
        if($id = $this->getRequest()->getParam('magazine_id')) {
            try {
                $model = Mage::getModel('flippingbook/magazine');
                $model->load($id);
                $model->delete();

                $this->_getSession()->addSuccess($this->__('Book was successfully deleted'));
                $this->_redirect('*/*/index');
                return;

            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array ('magazine_id' => $id));
                return;
            }
        }

        $this->_getSession()->addError($this->__('Unable to find a Book to delete'));

        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('magazinetable');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Please select items.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $item = Mage::getModel('flippingbook/magazine')->load($id);
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

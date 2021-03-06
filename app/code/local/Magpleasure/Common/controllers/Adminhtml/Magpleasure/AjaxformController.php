<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Magpleasure_Common
 */

class Magpleasure_Common_Adminhtml_Magpleasure_AjaxformController
    extends Magpleasure_Common_Controller_Adminhtml_Action_Service
{
    protected function _notifyAboutExpiry()
    {
        $this->_ajaxResponse(array(
            'expired' => true,
            'redirect' => $this->_getRefererUrl(),
        ));

        $this->getResponse()->sendResponse();
        exit(0);
    }

    public function preDispatch()
    {
        Mage::getSingleton('core/session', array('name'=>'adminhtml'));
        if (!Mage::getSingleton('admin/session')->isLoggedIn()){
            $this->_notifyAboutExpiry();
        }

        return parent::preDispatch();
    }

    public function loadAction()
    {
        $result = array();

        $errorMsg = $this->_commonHelper()->__("Edit form wasn't found.");
        $container = $this->getRequest()->getPost('container');
        $paramKey = $this->getRequest()->getPost('param_key');

        if ($container){
            /** @var $formContainer Magpleasure_Common_Block_Adminhtml_Widget_Ajax_Form_Container */
            $formContainer = $this->getLayout()->createBlock($container);
            if ($formContainer && ($formContainer instanceof Magpleasure_Common_Block_Adminhtml_Widget_Ajax_Form_Container)){

                $formContainer->setFormData($this->getRequest()->getPost());
                $formContainer->setHtmlId($this->getRequest()->getPost('html_id'));

                $entityId =
                    $this->getRequest()->getParam($paramKey) ?
                    $this->getRequest()->getParam($paramKey) :
                    $this->getRequest()->getPost('default_entity_id');

                if ($entityId){
                    try {
                        $formContainer->onLoad($entityId);
                    } catch (Exception $e){
                        $result['error'] = true;
                        $this->_getSession()->addError($e->getMessage());
                        $this->_commonHelper()->getException()->logException($e);
                        return ;
                    }
                }

                $result['title'] = $formContainer->getHeaderText();
                $result['html'] = $formContainer->toHtml();
                $result['success'] = true;

            } else {
                $result['error'] = true;
                $this->_getSession()->addError($errorMsg);
            }
        } else {
            $result['error'] = true;
            $this->_getSession()->addError($errorMsg);
        }

        $result['messages'] = $this->_getMessageBlockHtml();
        $this->_ajaxResponse($result);
    }

    public function saveAction()
    {
        $result = array();

        if ($covering = $this->getRequest()->getPost('covering')){

            $savedData = $this->_commonHelper()->getHash()->getObjectFromHash($covering);
            $post = $this->getRequest()->getPost();
            if (isset($post['covering'])) {
                unset($post['covering']);
            }

            if ($container = $savedData->getContainer()){

                /** @var $formContainer Magpleasure_Common_Block_Adminhtml_Widget_Ajax_Form_Container */
                $formContainer = $this->getLayout()->createBlock($container);
                if ($formContainer && ($formContainer instanceof Magpleasure_Common_Block_Adminhtml_Widget_Ajax_Form_Container)){

                    try {
                        $paramKey = $savedData->getParamKey();

                        $entityId =
                            $this->getRequest()->getParam($paramKey) ?
                            $this->getRequest()->getParam($paramKey) :
                            $this->getRequest()->getPost('default_entity_id');

                        $formContainer->onSave($entityId, $post);
                        $result['success'] = true;
                        $this->_getSession()->addSuccess($this->_commonHelper()->__($savedData->getSuccessMessage()));

                    } catch (Exception $e) {
                        $result['error'] = true;
                        $this->_getSession()->addError($e->getMessage());
                        $this->_commonHelper()->getException()->logException($e);
                    }

                } else {
                    $result['error'] = true;
                    $this->_getSession()->addError($this->_commonHelper()->__("Posted data has been broken."));
                }
            }

        } else {
            $result['error'] = true;
            $this->_getSession()->addError($this->_commonHelper()->__("Internal error due to saving."));
        }

        $result['messages'] = $this->_getMessageBlockHtml();
        $this->_ajaxResponse($result);
    }
}
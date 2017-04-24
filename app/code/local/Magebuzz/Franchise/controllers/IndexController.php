<?php
class Magebuzz_Franchise_IndexController extends Mage_Core_Controller_Front_Action{
    public function showformAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function sendemailAction(){
        $param = $this->getRequest()->getPost();
        if(!$param){
            return;
        }
        else{
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $_response = array();
            if (!$this->_validateFormKey()) {
                $_response['success'] = 'false';
                $_response['message'] =  $this->__('Form key is not valid');
                $this->getResponse()->setBody(json_encode($_response));
                return;
            }
            try {
                $model = Mage::getModel('franchise/franchise');
                $model
                    ->setData($param)
                    ->setId(NULL)
                    ->save();
                //-------------
                $translate = Mage::getSingleton('core/translate');
                $translate->setTranslateInline(false);
                $mailTemplate = Mage::getModel('core/email_template');

                $storeId = Mage::app()->getStore()->getId();

                $postObject = new Varien_Object();
                $postObject->setData($param);

                $template = Mage::getStoreConfig('franchise/general/email_template', $storeId);

                $sender  = array(
                    'name'  => Mage::getStoreConfig('trans_email/ident_support/name',$storeId),
                    'email' => Mage::getStoreConfig('trans_email/ident_support/email',$storeId)
                );

                $recepientEmail = Mage::getStoreConfig('franchise/general/receive_applicants',$storeId);

                $emailVariable = $postObject;

                $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                    ->sendTransactional(
                        $template, $sender, $recepientEmail, null, array('data' => $emailVariable)
                    );
                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $_response['success'] = 'true';
                $_response['success_message'] = 'Sent email successful.';
            }catch (Exception $e) {
                $_response['success'] = 'false';
                $_response['error_message'] =  $e->getMessage();
                $_response['error_code'] =  $e->getCode();
            }
            $this->getResponse()->setBody(json_encode($_response));
            return;
        }
        return;
    }
}
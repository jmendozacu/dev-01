<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Customform
 */

class Amasty_Customform_FormController extends Mage_Core_Controller_Front_Action
{
    const XML_PATH_EMAIL_RECIPIENT  = 'amcustomform/notif/recipient_email';
    const XML_PATH_EMAIL_SENDER     = 'amcustomform/notif/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'amcustomform/notif/email_template';
    const XML_PATH_ENABLED          = 'amcustomform/notif/enabled';

    public function resetAction(){
        $session = Mage::getSingleton('customer/session');
        $formId = $this->getRequest()->getParam('form_id');
        if($formId){
            $session->setData('customer-form-data-'.$formId,array());
        }
        $this->_redirectUrl($this->_getRefererUrl());
    }

    public function submitAction()
    {
        set_time_limit(600);
        ini_set('max_execution_time', 600);

        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton('customer/session');

        $postData = $this->getRequest()->getPost();
        $formId = $postData['form_id'];
        /** @var Amasty_Customform_Model_Form $form */
        $form = Mage::getModel('amcustomform/form')->load($formId);
        if($this->getRequest()->getParam('preview')){
            $this->_redirectUrl($form->getSuccessUrl());
            return;
        }
        try{
            $captchaModel = Mage::getModel('amcustomform/captcha',array('formId'=>'cap-custom-form-'.$formId));
            if($captchaModel->isRequired() && $form->getCaptcha()){
                $captchaKey = 'cap-custom-form-'.$formId;
                $word = $postData['captcha'][$captchaKey];

                if(!$captchaModel->isCorrect($word)){
                    throw new Exception('Captcha is not correct');
                }
            }
        }catch (Exception $e){
            $this->_getSession()->addError($e->getMessage());
            $session->setData('customer-form-data-'.$formId,$postData);
            $this->_redirectUrl($this->_getRefererUrl());
            return;
        }

        $uploadId = current(array_keys($_FILES));
        if(isset($_FILES[$uploadId]['name']) && ($_FILES[$uploadId]['tmp_name'] != NULL))
        {
            //check error when upload
            if(isset($_FILES[$uploadId]['error']) && $_FILES[$uploadId]['error'] > 0){
                Mage::getSingleton('core/session')->addError($this->__('Image Saving Error!'));
                $session->setData('customer-form-data-'.$formId,$postData);
                $this->_redirectUrl($this->_getRefererUrl());
                return;
            }

            try
            {    

                $path = Mage::getBaseDir('media') . DS ."amcustomform" . DS . $formId;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $uploader = new Varien_File_Uploader($uploadId);
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); 
                $uploader->setAllowRenameFiles(true);
                // Set the file upload mode 
                // false -> get the file directly in the specified folder
                // true -> get the file in the product like folders 
                //  (file.jpg will go in something like /media/f/i/file.jpg)
                $uploader->setFilesDispersion(false);
                $uploader->save($path, $_FILES[$uploadId]['name']);
                if ($uploadFile = $uploader->getUploadedFileName()) {
                    //save server file path
                    $postData[$uploadId] = $path . DS . $uploadFile;
                }
                else {
                    throw new Exception("Image Saving Error!!");      
                }
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $session->setData('customer-form-data-'.$formId, $postData);
                $this->_redirectUrl($this->_getRefererUrl());
                return;
            }
        }

        try
        {
            /** @var Amasty_Customform_Model_Form_Submit $submit */
            $submit = Mage::getModel('amcustomform/form_submit');
            $customerId = $session->isLoggedIn() ? $session->getCustomerId() : null;

            $session->setData('customer-form-data-'.$formId,array());
            unset($postData['form_id']);

            $submit->setFormId($formId);
            $submit->setStoreId(Mage::app()->getStore()->getStoreId());
            $submit->setSubmitted(time());
            $submit->setCustomerId($customerId);
            if(!$submit->setValues($postData)){
                throw new Exception('Not Valid');
            }
            $submit->setIp(Mage::app()->getRequest()->getClientIp(false));
            $submit->saveFiles();
            $submit->save();
            if($form->getNotification()){
                $this->sendEmail($submit);
            }
            $this->_getSession()->addSuccess('The form is saved successfully');
            $this->_redirectUrl($form->getSuccessUrl());
        }
        catch (Exception $e) {
            //$this->_getSession()->addError($e->getMessage());
            $session->setData('customer-form-data-'.$formId,$postData);
            $this->_redirectUrl($this->_getRefererUrl());
        }
    }

    protected function _getSession()
    {
        return Mage::getSingleton('core/session');
    }

    protected function sendEmail($submit){
        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $mailTemplate = Mage::getModel('core/email_template');

        if(!Mage::getStoreConfig(self::XML_PATH_ENABLED)) return;
        if($submit->getCustomerId()){
            $customer = Mage::getModel('customer/customer')->load($submit->getCustomerId());
            $userName = $customer->getFirstname()." ".$customer->getLastname();
        }else{
            $userName = "Guest";
        }
        $mailTemplate->setDesignConfig(array('area' => 'frontend'))
            ->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                null,
                array(
                    'link'=> "<a href='". Mage::helper("adminhtml")->getUrl('adminhtml/submit/view',array('id'=>$submit->getId(),'key'=>Mage::getSingleton('adminhtml/url')->getSecretKey()))."'>Link</a>",
                    'userName' => $userName
                    )
            );

        if (!$mailTemplate->getSentSuccess()) {
            throw new Exception();
        }
    }

    public function previewAction()
    {
        /** @var Amasty_Customform_Model_Form $form */
        $form = Mage::getModel('amcustomform/form');
        $form->setData($this->getRequest()->getPost());
        $form->realizeRelationData();

        Mage::register('amcustomform_preview_form', $form);

        $formBlock = Mage::app()->getLayout()->createBlock('amcustomform/form','form',array('preview'=>true));

        $this->loadLayout();
        $this->getLayout()->getBlock('content')->append($formBlock);
        $this->renderLayout();
    }

}
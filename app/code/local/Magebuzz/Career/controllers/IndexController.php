<?php
class Magebuzz_Career_IndexController extends Mage_Core_Controller_Front_Action{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function applicationAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function submitApplicationAction(){
        $params = $this->getRequest()->getPost();
        $job_id = $this->getRequest()->getParam('job_id');
        if(!$params){
            return;
        }
        else{
            try {
                $model = Mage::getModel('career/application');
                $model
                  ->setData($params)
                  ->setApplicationForJobId($job_id)
                  ->setId(NULL)
                  ->save();
                Mage::getSingleton('core/session')->addSuccess($this->__('Apply successfully!'));
                $this->_redirect('*/*/index');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/');
                return;
            }
        }
    }
}
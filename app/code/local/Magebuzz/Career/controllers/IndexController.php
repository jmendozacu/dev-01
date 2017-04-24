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
        $job_title = $this->getRequest()->getParam('job_title');
        if(!$params){
            return;
        }
        else{
            try {
                $model = Mage::getModel('career/application');
                $model
                  ->setData($params)
                  ->setApplicationForJobId($job_id)
                  ->setApplicationForJob($job_title)
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

    public function filterproductAction()
    {
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $response = array();
        $collection = Mage::getBlockSingleton('career/job')->getCollection();
        $response['result'] = Mage::app()->getLayout()->createBlock('career/job')
          ->setCollection($collection)
          ->setTemplate('career/contentajaxpager.phtml')->toHTML();
        $response['success'] = 'true';
        $this->getResponse()->setBody(json_encode($response));
        return;
    }
    public function pagerMobieAction()
    {
        $current_page = $this->getRequest()->getParam('p');
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $response = array();
        $collection = Mage::getBlockSingleton('career/job')->getCollection();
        $response['result'] = Mage::app()->getLayout()->createBlock('career/job')
          ->setCollection($collection)
          ->setTemplate('career/contentajaxpager-mobie.phtml')->toHTML();
        $response['success'] = 'true';
        $response['next_page'] = $current_page + 1;
        $this->getResponse()->setBody(json_encode($response));
        return;
    }
}
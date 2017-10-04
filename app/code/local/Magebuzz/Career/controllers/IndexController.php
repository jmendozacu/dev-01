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
                $fileName = '';
                if (isset($_FILES['cp-attachment']['name']) && $_FILES['cp-attachment']['name'] != '') {
                    try {
                        $now = date('ymdHis');
                        $fileName = $now.'_'.$_FILES['cp-attachment']['name'];
                        $uploader = new Varien_File_Uploader('cp-attachment');
                        $uploader->setAllowedExtensions(array('doc', 'docx', 'pdf', 'jpg', 'png', 'zip', 'gif','txt','xlsx','rar')); //add more file types you want to allow
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $path = Mage::getBaseDir('media') . DS . 'career';
                        if (!is_dir($path)) {
                            mkdir($path, 0777, true);
                        }
                        $res = $uploader->save($path . DS, $fileName);
                        if(isset($res['file'])){
                            $fileName = $res['file'];
                        }
                        $attachmentFilePath = Mage::getBaseDir('media') . DS . 'career' . DS . $fileName;
                    } catch (Exception $e) {
                        Mage::getSingleton('customer/session')->addError($e->getMessage());
                    }
                }
                $model = Mage::getModel('career/application');
                $model
                  ->setData($params)
                  ->setAttachment($fileName)
                  ->setCreatedAt(Mage::getModel('core/date')->date('Y-m-d'))
                  ->setApplicationForJobId($job_id)
                  ->setApplicationForJob($job_title)
                  ->setId(NULL)
                  ->save();
                Mage::helper('career')->sendEmail($params,$job_title);
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
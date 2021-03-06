<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Dealerlocator_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action {
  public function indexAction() {
    $this->loadLayout()->renderLayout();
  }

  public function saveAction() {
    if ($data = $this->getRequest()->getPost()) {
      if (isset($_FILES['csv_file']['name']) && $_FILES['csv_file']['name'] != '') {
        try {
          $uploader = new Varien_File_Uploader('csv_file');
          $uploader->setAllowedExtensions(array('csv'));
          $uploader->setAllowRenameFiles(FALSE);
          $uploader->setFilesDispersion(FALSE);
          $path = Mage::getBaseDir('media') . DS . 'dealers' . DS;
          $uploader->save($path, $_FILES['csv_file']['name']);
          $new_file_name = $uploader->getUploadedFileName();
          $filepath = $path . $new_file_name;
          $handler = new Varien_File_Csv();
          $importData = $handler->getData($filepath);
          $keys = $importData[0];
          foreach ($keys as $key => $value) {
            $keys[$key] = str_replace(' ', '_', strtolower($value));
          }
          $count = count($importData);
          $model = Mage::getModel('dealerlocator/dealerlocator');
          while (--$count > 0) {
            $currentData = $importData[$count];
            $data = array_combine($keys, $currentData);
            array_shift($data);

            // set default store & status
            $data['stores'] = 0;
            $data['status'] = 1;

            $model->setData($data)->save();
          }
        } catch (Exception $e) {
          //do nothing here
        }
      }
      Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dealerlocator')->__('Successfully saved'));
      $this->_redirect('*/adminhtml_dealerlocator/index');
    }
  }
}
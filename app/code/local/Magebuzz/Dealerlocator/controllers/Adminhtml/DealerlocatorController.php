<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Dealerlocator_Adminhtml_DealerlocatorController extends Mage_Adminhtml_Controller_Action {
  protected function _initAction() {
    $this->loadLayout()->_setActiveMenu('dealerlocator/items')->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
    return $this;
  }

  public function indexAction() {
    $this->_initAction()->renderLayout();
  }

  public function editAction() {
    $id = $this->getRequest()->getParam('id');
    $model = Mage::getModel('dealerlocator/dealerlocator')->load($id);

    if ($model->getId() || $id == 0) {
      $data = Mage::getSingleton('adminhtml/session')->getFormData(TRUE);
      if (!empty($data)) {
        $model->setData($data);
      }

      Mage::register('dealerlocator_data', $model);
      $this->loadLayout();
      $this->_setActiveMenu('dealerlocator/items');

      $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
      $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

      $this->getLayout()->getBlock('head')->setCanLoadExtJs(TRUE);

      $this->_addContent($this->getLayout()->createBlock('dealerlocator/adminhtml_dealerlocator_edit'))->_addLeft($this->getLayout()->createBlock('dealerlocator/adminhtml_dealerlocator_edit_tabs'));

      $this->renderLayout();
    } else {
      Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Item does not exist'));
      $this->_redirect('*/*/');
    }
  }

  public function newAction() {
    $this->_forward('edit');
  }

  public function saveAction() {
    if ($data = $this->getRequest()->getPost()) {
      $model = Mage::getModel('dealerlocator/dealerlocator');
      if ($id = $this->getRequest()->getParam('id')) {
        $model->load($id);
      }

      if (!$data['longitude'] || !$data['latitude'] || $data['address'] != $model->getAddress()) {
        $address = urlencode($data['address']);
        $json = Mage::helper('dealerlocator')->getJsonData($address);
				if(!empty($json->{'results'})){
					$data['latitude'] = strval($json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'});
					$data['longitude'] = strval($json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'});
				}
      }

      if (isset($_FILES['icon_image']['name']) && $_FILES['icon_image']['name'] != '') {
        try {
          //rename image in case image name has space
          $image_name = $_FILES['icon_image']['name'];

          $uploader = new Varien_File_Uploader('icon_image');
          $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
          $uploader->setAllowRenameFiles(TRUE);
          $uploader->setFilesDispersion(FALSE);

          $path = Mage::getBaseDir('media') . DS . 'dealers' . DS . 'icons';
          if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
          }

            $uploader->save($path, $image_name);

          $new_icon_image = $uploader->getUploadedFileName();
					
        } catch (Exception $e) {
          // silence is gold
        }
        $data['icon_image'] = $new_icon_image;
      } elseif ($model->getIconImage()) {
        $data['icon_image'] = $model->getIconImage();
      }
			
      if (isset($_FILES['dealer_map']['name']) && $_FILES['dealer_map']['name'] != '') {
        try {
          //rename image in case image name has space
          $image_name = $_FILES['dealer_map']['name'];

          $uploader = new Varien_File_Uploader('dealer_map');
          $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
          $uploader->setAllowRenameFiles(TRUE);
          $uploader->setFilesDispersion(FALSE);

          $path = Mage::getBaseDir('media') . DS . 'dealers' . DS . 'map';
          if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
          }

          $uploader->save($path, $image_name);
          $new_dealermap_name = $uploader->getUploadedFileName();
        } catch (Exception $e) {
          // silence is gold
        }
        $data['dealer_map'] = $new_dealermap_name;
      } elseif ($model->getDealerMap()) {
        $data['dealer_map'] = $model->getDealerMap();
      }

      $post = $this->getRequest()->getPost();
      if (isset($post['icon_image']['delete']) && $post['icon_image']['delete'] == 1) {
        $data['icon_image'] = '';
      }
			
			if (isset($post['dealer_map']['delete']) && $post['dealer_map']['delete'] == 1) {
        $data['dealer_map'] = '';
      }

      $model->setData($data)->setId($this->getRequest()->getParam('id'));

      $dealerTag = $model->getDealerTag();
      if ($dealerTag != '') {
        $tagArray = explode(',', $dealerTag);
        $tagArray = array_map('trim', $tagArray);
        $model->setDealerTag($tagArray);
      } else {
        $model->setDealerTag(NULL);
      }

      try {
        $model->save();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
        Mage::getSingleton('adminhtml/session')->setFormData(FALSE);

        if ($this->getRequest()->getParam('back')) {
          $this->_redirect('*/*/edit', array('id' => $model->getId()));
          return;
        }
        $this->_redirect('*/*/');
        return;
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        Mage::getSingleton('adminhtml/session')->setFormData($data);
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        return;
      }
    }
    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dealerlocator')->__('Unable to find item to save'));
    $this->_redirect('*/*/');
  }

  public function deleteAction() {
    if ($this->getRequest()->getParam('id') > 0) {
      try {
        $model = Mage::getModel('dealerlocator/dealerlocator');

        $model->setId($this->getRequest()->getParam('id'))->delete();

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
        $this->_redirect('*/*/');
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
      }
    }
    $this->_redirect('*/*/');
  }

  public function massDeleteAction() {
    $dealerlocatorIds = $this->getRequest()->getParam('dealerlocator');
    if (!is_array($dealerlocatorIds)) {
      Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
    } else {
      try {
        foreach ($dealerlocatorIds as $dealerlocatorId) {
          $dealerlocator = Mage::getModel('dealerlocator/dealerlocator')->load($dealerlocatorId);
          $dealerlocator->delete();
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($dealerlocatorIds)));
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }
    }
    $this->_redirect('*/*/index');
  }

  public function massStatusAction() {
    $dealerlocatorIds = $this->getRequest()->getParam('dealerlocator');
    if (!is_array($dealerlocatorIds)) {
      Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
    } else {
      try {
        foreach ($dealerlocatorIds as $dealerlocatorId) {
          $dealerlocator = Mage::getSingleton('dealerlocator/dealerlocator')->load($dealerlocatorId)->setStatus($this->getRequest()->getParam('status'))->setIsMassupdate(TRUE)->save();
        }
        $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully updated', count($dealerlocatorIds)));
      } catch (Exception $e) {
        $this->_getSession()->addError($e->getMessage());
      }
    }
    $this->_redirect('*/*/index');
  }

  public function exportCsvAction() {
    $fileName = 'dealerlocator.csv';
    $content = $this->getLayout()->createBlock('dealerlocator/adminhtml_dealerlocator_grid')->getCsv();

    $this->_sendUploadResponse($fileName, $content);
  }

  public function exportXmlAction() {
    $fileName = 'dealerlocator.xml';
    $content = $this->getLayout()->createBlock('dealerlocator/adminhtml_dealerlocator_grid')->getXml();

    $this->_sendUploadResponse($fileName, $content);
  }

  protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
    $response = $this->getResponse();
    $response->setHeader('HTTP/1.1 200 OK', '');
    $response->setHeader('Pragma', 'public', TRUE);
    $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', TRUE);
    $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
    $response->setHeader('Last-Modified', date('r'));
    $response->setHeader('Accept-Ranges', 'bytes');
    $response->setHeader('Content-Length', strlen($content));
    $response->setHeader('Content-type', $contentType);
    $response->setBody($content);
    $response->sendResponse();
    die;
  }
}
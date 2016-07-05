<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */  
class MarginFrame_Shopby_Adminhtml_ValueController extends Mage_Adminhtml_Controller_Action
{
    protected $small_width;
    protected $small_height;
    protected $big_width;
    protected $big_height;

    public function _construct(){
        $confAttr = Mage::getModel('amconf/attribute')->load(92, 'attribute_id');
        $this->small_width = $confAttr->getSmallWidth();
        $this->small_height = $confAttr->getSmallHeight();
        $this->big_width = $confAttr->getBigWidth();
        $this->big_height = $confAttr->getBigHeight();
    }
    // edit filters (uses tabs)
    public function editAction() 
    {
        $id     = (int) $this->getRequest()->getParam('id');
        /** @var MarginFrame_Shopby_Model_Value $model */
        $model  = Mage::getModel('amshopby/value')->load($id);

        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Option does not exist'));
            $this->_redirect('*/adminhtml_filter/index');
            return;
        }
        
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        // todo: save images
        
        Mage::register('amshopby_value', $model);

        $this->loadLayout();
        
        $this->_setActiveMenu('catalog/amshopby');
        $this->_addContent($this->getLayout()->createBlock('amshopby/adminhtml_value_edit'));

        $this->_title($model->getCurrentTitle() . $this->__(' Settings'));

        $this->renderLayout();
    }

    public function saveAction() 
    {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('amshopby/value')
                   ->load($id);
        $filterId = $model->getFilterId();
        $optionId = $model->getOptionId();
        $data = $this->getRequest()->getPost();

        if (isset($data['multistore'])){
            foreach ($data['multistore'] as $key=>$value){
                $data[$key] = serialize($value);
            }
        }
        if (!$data) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Unable to find an option to save'));
            $this->_redirect('*/adminhtml_filter/');
        }

        $uploadDir = Mage::getBaseDir('media') . DS . 'amconf' . DS . 'images' . DS;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        /**
         * Deleting
         */
        $toDelete = Mage::app()->getRequest()->getPost('amconf_icon_delete');
        if ($toDelete)
        {

                    $swatchModel = Mage::getModel('amconf/swatch')->load($optionId);
                    $this->deleteImage($optionId, $swatchModel->getExtension());
                    // delete swatch from table
                    $swatchModel->delete();
        }

        /**
         * Uploading files
         */
        if (isset($_FILES['amconf_icon']) && isset($_FILES['amconf_icon']['error']))
        {
            if (0 == $_FILES['amconf_icon']['error'])
            {
                $file = explode('.', $_FILES['amconf_icon']['name']);
                $extension = end($file);
                $swatchModel = Mage::getModel('amconf/swatch')->load($optionId);

                // delete old img before upload new img
                $this->deleteImage($optionId, $swatchModel->getExtension());

                // save extension in table
                $swatchModel->setAttributeId($optionId);
                $swatchModel->setColor(null);
                $swatchModel->setExtension($extension);
                $swatchModel->save();

                move_uploaded_file($_FILES['amconf_icon']['tmp_name'], $uploadDir . $optionId . '.' . $extension);

                if (!file_exists($uploadDir . $optionId . '.' . $extension))
                {
                    Mage::getSingleton('catalog/session')->addSuccess('File was not uploaded. Please check permissions to folder media/amconf/images(need 0777 recursively)');
                }
            }
        }

        //add color
        if(isset($data['color_swatch']) && $data['color_swatch']){
            $swatchModel = Mage::getModel('amconf/swatch')->load($optionId);
            $swatchModel->setAttributeId($optionId);
            $swatchModel->setColor($data['color_swatch']);
            $swatchModel->setExtension(null);
            $swatchModel->save();
            $this->deleteImage($optionId, $swatchModel->getExtension());
        }
        //upload images
        $path = Mage::getBaseDir('media') . DS . 'amshopby' . DS;
        $imagesTypes = array('big', 'small', 'medium', 'small_hover');
        foreach ($imagesTypes as $type){
            $field = 'img_' . $type;
            
            $isRemove = isset($data['remove_' . $field]);
            $hasNew   = !empty($_FILES[$field]['name']);
            
            try {
                // remove the old file
                if ($isRemove || $hasNew){
                    $oldName = $model->getData($field);
                    if ($oldName){
                         @unlink($path . $oldName);
                         $data[$field] = '';
                    }
                }
    
                // upload a new if any
                if (!$isRemove && $hasNew){
                    $newName = $type . $id;
                    $newName .= '.' . strtolower(substr(strrchr($_FILES[$field]['name'], '.'), 1)); 
               
                    $uploader = new Varien_File_Uploader($field);
                    $uploader->setFilesDispersion(false);
                    $uploader->setAllowRenameFiles(false);
                       $uploader->setAllowedExtensions(array('png','gif', 'jpg', 'jpeg'));
                    $uploader->save($path, $newName);    
                     
                    $data[$field] = $newName;            
                }   
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());    
            }
        }
        try {
            $model->setData($data)->setId($id);
            
            $model->save();
            Mage::getSingleton('adminhtml/session')->setFormData(false);
            
            $msg = Mage::helper('amshopby')->__('Option properties have been successfully saved');
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);

            if ($this->getRequest()->getParam('continue')){
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
            }
            else {
                $this->_redirect('*/adminhtml_filter/edit', array('id'=>$filterId, 'tab'=>'values'));
            }

        } 
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $id));
        }

        $this->invalidateCache();
    }

    protected function invalidateCache()
    {
        /** @var MarginFrame_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        $helper->invalidateCache();
    }

    protected function deleteImage($optionId, $extension) {
        $uploadDir = Mage::getBaseDir('media') . DS . 'amconf' . DS . 'images' . DS;
        if (file_exists($uploadDir . $optionId . '.' . $extension)) {
            @unlink($uploadDir . $optionId . '.' . $extension);
        } elseif(file_exists($uploadDir . $optionId . '.jpg')) {
            @unlink($uploadDir . $optionId . '.jpg');
        }
        $this->deleteCacheImg($optionId, $extension,$this->small_width , $this->small_height);
        $this->deleteCacheImg($optionId, $extension, $this->big_width, $this->big_height);
    }

    protected function deleteCacheImg($optionId, $extension, $width, $height) {
        $uploadDir = Mage::getBaseDir('media') . DS . 'amconf' . DS . 'images' . DS;
        $img = $uploadDir . $optionId . '.' . $extension;
        $cacheDir = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . DS .'cache' . DS;
        $cacheImg = $cacheDir . md5($img . $width . $height) . '.' . $extension;
        if (file_exists($cacheImg)) {
            @unlink($cacheImg);
        } else {
            $img = $uploadDir . $optionId . '.jpg';
            $cacheImg = $cacheDir . md5($img . $width . $height) . '.jpg';
            if (file_exists($cacheImg)) {
                @unlink($cacheImg);
            }
        }
    }

}
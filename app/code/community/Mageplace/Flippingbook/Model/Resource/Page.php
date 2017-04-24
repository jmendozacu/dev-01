<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Model_Resource_Page extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('flippingbook/page', 'page_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        if (!empty($_FILES) && !$object->getMultiupload()) {
            $magazine_model = Mage::getModel('flippingbook/magazine')->load($object->getPageMagazineId());

            foreach ($_FILES as $key => $file) {
                $config_path = strtolower(preg_replace('/^.*\_/', '', $key));
                $config_path = $config_path == 'image' ? 'page' : $config_path;
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $uploader = new Varien_File_Uploader($key);
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->addValidateCallback('catalog_product_image', Mage::helper('catalog/image'), 'validateUploadFile');
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $result = $uploader->save(
                        Mage::helper('flippingbook')->getDir($config_path) . DS . $magazine_model->getMagazineImgsubfolder()
                    );

                    if ($object->getData($key)) {
                        $img_file = $object->getData($key);

                        if (!empty($img_file['value']) && !empty($img_file['delete'])) {
                            $img_file_path = str_replace(Mage::helper('flippingbook')->getPathUrl($config_path) . '/', '', strval($img_file['value']));
                            @unlink(Mage::helper('flippingbook')->getDir($config_path) . DS . $img_file_path);
                        }
                    }

                    $object->setData($key, $magazine_model->getMagazineImgsubfolder() . $result['file']);

                } else if ($file['error'] === UPLOAD_ERR_NO_FILE) {
                    $img_file = $object->getData($key);

                    if (!empty($img_file['value'])) {
                        $img_file_path = str_replace(Mage::helper('flippingbook')->getPathUrl($config_path) . '/', '', strval($img_file['value']));

                        if (empty($img_file['delete'])) {
                            $img_file = $img_file_path;
                        } else {
                            @unlink(Mage::helper('flippingbook')->getDir($config_path) . DS . $img_file_path);
                            $img_file = '';
                        }

                    } else {
                        $img_file = '';
                    }


                    $object->setData($key, $img_file);
                }
            }
        }

        switch ($object->getPageType()) {
            case 'Image':
                $object->setData('page_text', null);
                if (!$object->getData('page_image')) {
                    throw new Exception(Mage::helper('flippingbook')->__('Please select an image file.'), 0);
                }
                break;

            case 'Text':
                $object->setData('page_image', null);
                if (!$object->getData('page_text')) {
                    throw new Exception(Mage::helper('flippingbook')->__('Please enter a page text.'), 0);
                }
                break;

            default:
                throw new Exception(Mage::helper('flippingbook')->__('Please select a page content.'), 0);
        }

        return $this;
    }

    public function getPageNameById($id)
    {
        $select = $this->_getReadAdapter()->select();
        /* @var $select Zend_Db_Select */
        $select->from(array('main_table' => $this->getMainTable()), 'page_title')
            ->where('main_table.page_id = ?', $id);

        return $this->_getReadAdapter()->fetchOne($select);
    }

}
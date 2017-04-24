<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Model_Page extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('flippingbook/page');
    }

    public function getName()
    {
        return $this->getPageTitle();
    }

    public function saveMultiupload($post, $magazine, $files_ids, $allowed_extensions)
    {
        $page_dir          = Mage::helper('flippingbook')->getDir('page');
        $magazine_page_dir = $page_dir . DS . $magazine->getMagazineImgsubfolder();
        $page_sort_order   = Mage::helper('flippingbook')->getLastPostition($magazine);
        $page_counter      = 1;
        foreach ($files_ids as $files_id) {
            $this->unsetData();

            $uploader = new Mageplace_Flippingbook_Model_File_Uploader($files_id);
            $uploader->setAllowedExtensions($allowed_extensions);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $uploader->setFilesUploadMode(empty($post['delete_files']) ? 'copy' : 'rename');

            $result = $uploader->save($magazine_page_dir);

            if (!empty($result['path']) && !empty($result['file'])) {
                try {
                    $size       = $magazine->getResolutionWidth() . '__' . $magazine->getResolutionHeight();
                    $imageModel = Mage::getModel('flippingbook/product_image');
                    $imageModel->setSuffix('_' . $size)
                        ->setSize($size)
                        ->setBaseFile($result['path'] . $result['file'])
                        ->resize()
                        ->saveFile();
                } catch (Exception $e) {
                    $this->setData('error_saved_pages', $e);
                    return $this;
                }
            }

            try {
                $this->setMultiupload(true)
                    ->setData('page_magazine_id', $magazine->getId())
                    ->setData('page_title', $post['page_title'] . ' ' . $page_counter)
                    ->setData('page_type', 'Image')
                    ->setData('page_sort_order', $page_sort_order)
                    ->setData('page_image', str_replace($page_dir . DS, '', $imageModel->getNewFile()))
                    ->save();
            } catch (Exception $e) {
                $this->setData('error_saved_pages', $e);
                return $this;
            }
            $page_sort_order++;
            $page_counter++;
        }

        $this->setData('saved_pages', $page_counter - 1);

        return $this;
    }

    public function getIsImage()
    {
        if($this->getpageType() == 'Image'){
            return true;
        }
        return false;
    }

    public function getPageImageUrl()
    {
        $page_image_path = Mage::helper('flippingbook')->getPathUrl('page');
        $url  = $page_image_path.'/'.$this->getPageImage();
        $url = str_replace('\\','/',$url); // fix for turn.js library
        return $url;
    }

}
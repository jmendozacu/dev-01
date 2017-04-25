<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Adminhtml_Flippingbook_MultiuploadController extends Mage_Adminhtml_Controller_Action
{   
    protected function _initAction()
    {
        $this->_usedModuleName = 'flippingbook';

        $this->loadLayout()
            ->_setActiveMenu('flippingbook/multiupload')
            ->_title($this->__('HTML5 Flipping Book'))
            ->_title($this->__('Multiupload'))
            ->_addBreadcrumb($this->__('HTML5 Flipping Book'), $this->__('HTML5 Flipping Book'))
            ->_addBreadcrumb($this->__('Multiupload'), $this->__('Multiupload'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('flippingbook/adminhtml_multiupload'))
            ->renderLayout();
    }


    public function saveAction()
    {
        if((!$post = $this->getRequest()->getPost()) || empty($post['source_type'])
            || (($post['source_type'] == 'file') && empty($_FILES['upload_package']['size']))
            || (($post['source_type'] == 'dir') && empty($post['input_dir'])))
        {
            $this->_getSession()->addError($this->__('Please fill all required fields'));
            $this->_redirect('*/*/');
            return;
        }

        $magazine = Mage::getModel('flippingbook/magazine')->load(intval(@$post['magazine_id']));
        if(!$magazine->getId()) {
            $this->_getSession()->addError($this->__('Please fill all required fields'));
            $this->_redirect('*/*/');
            return;
        }

        $allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');
        if($post['source_type'] == 'dir') {
            if((!$post['input_dir'] = realpath($post['input_dir'])) && !is_dir($post['input_dir'])) {
                $this->_getSession()->addError($this->__('Please fill all required fields'));
                $this->_redirect('*/*/');
                return;
            }

            $files_ids = array();
            $dir = @ dir($post['input_dir']);
            while (($file = $dir->read()) !== false) {
                if(is_dir($dir->path.DS.$file)) {
                    continue;
                }

                $info = pathinfo($file);
                if(!empty($info['extension']) && in_array($info['extension'], $allowed_extensions)) {
                    $basefile = realpath($dir->path).DS.$info['basename'];
                    $img_file_params = array();
                    $img_file_params['name'] = $info['basename'];
                    $img_file_params['type'] = mime_content_type($basefile);
                    $img_file_params['tmp_name'] = $basefile;
                    $img_file_params['error'] = 0;
                    $img_file_params['size'] = filesize($basefile);

                    $tmp_name = $info['basename'];//uniqid();
                    $files_ids[] = $tmp_name;
                    $_FILES[$tmp_name] = $img_file_params;
                }
            }
            $dir->close();

            natsort($files_ids);

        } else {
            $file = $_FILES['upload_package'];
            if(($file['type'] != 'application/zip') && ($file['type'] != 'application/x-zip-compressed') && !preg_match("/(\.zip)$/is", $file['name'])) {
                $this->_getSession()->addError($this->__('Upload file must be zip archive'));
                $this->_redirect('*/*/');
                return;
            }

            if(!class_exists('ZipArchive', false)) {
                $this->_getSession()->addError($this->__('Unrecoverable error: Zip library missing'));
                $this->_redirect('*/*/');
                return;
            }

            $zip = zip_open($file['tmp_name']);
            if(!$zip) {
                $this->_getSession()->addError($this->__('Cannot open zip file'));
                $this->_redirect('*/*/');
                return;
            }

            $files_ids = array();
            while($zip_entry = zip_read($zip)) {
                if (zip_entry_open($zip, $zip_entry, "r")) {
                    $tmp_img_buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                    $tmp_img_file = tempnam(Mage::helper('flippingbook')->getDir('tmp'), 'image_');
                    if(file_put_contents($tmp_img_file, $tmp_img_buf)) {
                        $img_file_params = array();
                        $img_file_params['name'] = zip_entry_name($zip_entry);
                        $img_file_params['type'] = mime_content_type($tmp_img_file);
                        $img_file_params['tmp_name'] = $tmp_img_file;
                        $img_file_params['error'] = 0;
                        $img_file_params['size'] = zip_entry_filesize($zip_entry);

                        $tmp_name = basename($tmp_img_file);
                        $files_ids[] = $tmp_name;
                        $_FILES[$tmp_name] = $img_file_params;
                    }
                    zip_entry_close($zip_entry);
                }
            }

            $post['delete_files'] = 1;

            @unlink($file['tmp_name']);
            unset($_FILES['upload_package']);
        }

        $pages = Mage::getModel('flippingbook/page')->saveMultiupload($post, $magazine, $files_ids, $allowed_extensions);

        if($pages->getSavedPages()) {
            $message_method = 'addSuccess';
            $text = $this->__('Total of %d page(s) were created', $pages->getSavedPages());
        }elseif($pages->getErrorSavedPages()){
            $message_method = 'addError';
            $text = $pages->getErrorSavedPages()->getMessage();
        }else {
            $message_method = 'addWarning';
            $text = $this->__('Something wrong');
        }
        $this->_getSession()->$message_method($text);
        $this->_redirect('*/flippingbook_page/');
    }
}

if(!function_exists('mime_content_type')) {
    function mime_content_type($filename) {
        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );


        $filenames = explode('.', $filename);
        $ext = strtolower(array_pop($filenames));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}
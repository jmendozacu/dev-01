<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Dealerlocator_Adminhtml_ProductController extends Mage_Adminhtml_Controller_Action {
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
                    $path = Mage::getBaseDir('media') . DS . 'dealerlocator' . DS;
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
                    $model = Mage::getModel('dealerlocator/productdealertemp');

                    while (--$count > 0) {
                        $currentData = $importData[$count];
                        $data = array_combine($keys, $currentData);
                        array_shift($data);
                        if ((!$data['longitude'] || !$data['latitude']) && ($data['address'] || $data['postal_code'])) {
                            if ($data['address']) {
                                $address = urlencode($data['address']);
                                var_dump($address);
                            } else {
                                $address = urlencode($data['postal_code']);
                                var_dump($address);
                            }
                            $json = Mage::helper('dealerlocator')->getJsonData($address);
                            var_dump($json);
                            $data['latitude'] = strval($json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'});
                            $data['longitude'] = strval($json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'});
                            var_dump($data['latitude']); var_dump($data['longitude']);
                        }
                        if ($data['dealer_tag'] != '') {
                            $data['dealer_tag'] = explode(',', $data['dealer_tag']);
                            var_dump($data['dealer_tag']);
                        }
                        $model->setData($data)->save();
                    }

                    $query1 = "Update "  . $this->_getTableName('product_dealer_temp') . " as pdt INNER JOIN `dealerlocator` as d ON pdt.`store_code` = d.`store_code` SET pdt.`dealerlocator_id`= d.`dealerlocator_id`" ;
                    $this->_getWriteConnection()->query($query1);

                    $query2 = "Update " . $this->_getTableName('product_dealer_temp') ." as pdt INNER JOIN `catalog_product_entity` as cpe ON pdt.`product_sku`=cpe.`sku` set pdt.`product_id` =  cpe.`entity_id`" ;
                    $this->_getWriteConnection()->query($query2);


                    $query3 = "INSERT INTO " .$this->_getTableName('product_dealer')." (product_id,dealer_id) (SELECT product_id, dealerlocator_id FROM product_dealer_temp)" ;
                    $this->_getWriteConnection()->query($query3);
                } catch (Exception $e) {
                    //do nothing here
                }
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dealerlocator')->__('Successfully saved'));
            $this->_redirect('*/adminhtml_product/index');
        }
    }

    protected function _getWriteConnection() {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        return $writeConnection;
    }

    protected function _getReadConnection() {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        return $readConnection;
    }

    protected function _getTableName($name) {
        return Mage::getSingleton('core/resource')->getTableName($name);
    }


}
<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Model_Resource_Magazine extends Mage_Core_Model_Resource_Db_Abstract
{
    protected $_productId;

    protected function _construct()
    {
        $this->_init('flippingbook/magazine', 'magazine_id');
    }

    public function setProductId($product_id)
    {
        $this->_productId = $product_id;
    }


    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());

        $object->setMagazineImgsub(is_null($object->getMagazineImgsub()) ? 0 : 1);
        if (!is_null($object->getMagazineImgsub())) {
            if (!$object->getMagazineImgsubfolder()) {
                $object->setMagazineImgsubfolder(strtolower($object->getMagazineTitle()));
            }
            $object->setMagazineImgsubfolder(preg_replace('/[^a-z0-9\_]/i', '_', $object->getMagazineImgsubfolder()));
        } else {
            $object->setMagazineImgsubfolder('');
        }

        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $file) {
                $config_path = strtolower(preg_replace('/^.*\_/', '', $key));
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $uploader = new Varien_File_Uploader($key);
                    if ($config_path == 'pdf') {
                        $uploader->setAllowedExtensions(array('pdf'));
                    } else {
                        $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                        $uploader->addValidateCallback('catalog_product_image', Mage::helper('catalog/image'), 'validateUploadFile');
                    }
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);

                    $result = $uploader->save(
                        Mage::helper('flippingbook')->getDir($config_path) . DS . $object->getMagazineImgsubfolder()
                    );

                    if ($object->getData($key)) {
                        $img_file = $object->getData($key);

                        if (!empty($img_file['value']) && !empty($img_file['delete'])) {
                            $img_file_path = str_replace(Mage::helper('flippingbook')->getPathUrl($config_path) . '/', '', strval($img_file['value']));
                            @unlink(Mage::helper('flippingbook')->getDir($config_path) . DS . $img_file_path);
                        }

                    }

                    $object->setData($key, $object->getMagazineImgsubfolder() . $result['file']);

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


        return $this;
    }


    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('magazine_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('flippingbook/magazine_store'), $condition);

        foreach ((array)$object->getData('stores') as $store) {
            $this->_getWriteAdapter()
                ->insert(
                    $this->getTable('flippingbook/magazine_store'),
                    array(
                        'magazine_id' => $object->getId(),
                        'store_id'    => $store
                    )
                );
        }

        return $this;
    }


    protected function _getLoadSelect($field, $value, $object)
    {
        $selest = parent::_getLoadSelect($field, $value, $object);

        $selest->join(
            array(
                'resolution_table' => $this->getTable('flippingbook/resolution')
            ),
            $this->getMainTable() . '.magazine_resolution_id = resolution_table.resolution_id',
            array('resolution_width', 'resolution_height')
        )->join(
                array(
                    'template_table' => $this->getTable('flippingbook/template')
                ),
                $this->getMainTable() . '.magazine_template_id = template_table.template_id'
            );

        return $selest;
    }


    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()
            ->select()
            ->from(
                $this->getTable('flippingbook/magazine_store')
            )->where(
                'magazine_id = ?',
                $object->getId()
            );

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $stores = array();
            foreach ($data as $row) {
                $stores[] = $row['store_id'];
            }

            $object->setData('store_id', $stores);
        }

        if ($this->_productId) {
            $select = $this->_getReadAdapter()
                ->select()
                ->from(
                    $this->getTable('flippingbook/product_magazine')
                )->where(
                    'entity_id = ?',
                    $this->_productId
                )->where(
                    'magazine_id = ?',
                    $object->getId()
                );

            if ($data = $this->_getReadAdapter()->fetchRow($select)) {
                $object->setData('product_attached', 1);
            }
        }

        return $this;
    }


    public function attachMagazines($product_id, $magazine_ids)
    {
        if (empty($product_id) || empty($magazine_ids)) {
            return $this;
        }

        foreach ((array)$magazine_ids as $magazine_id) {
            $this->_getWriteAdapter()
                ->insert(
                    $this->getTable('flippingbook/product_magazine'),
                    array(
                        'magazine_id' => $magazine_id,
                        'entity_id'   => $product_id
                    )
                );
        }

        return $this;
    }


    public function detachMagazines($product_id, $magazine_ids)
    {
        if (empty($product_id) || empty($magazine_ids)) {
            return $this;
        }

        foreach ((array)$magazine_ids as $magazine_id) {
            $this->_getWriteAdapter()->delete(
                $this->getTable('flippingbook/product_magazine'),
                array(
                    $this->_getWriteAdapter()->quoteInto('entity_id = ?', $product_id),
                    $this->_getWriteAdapter()->quoteInto('magazine_id = ?', $magazine_id)
                )
            );
        }

        return $this;
    }


    public function getMagazineNameById($id)
    {
        $select = $this->_getReadAdapter()->select();
        /* @var $select Zend_Db_Select */
        $select->from(array('main_table' => $this->getMainTable()), 'magazine_title')
            ->where('main_table.magazine_id = ?', $id);

        return $this->_getReadAdapter()->fetchOne($select);
    }

}
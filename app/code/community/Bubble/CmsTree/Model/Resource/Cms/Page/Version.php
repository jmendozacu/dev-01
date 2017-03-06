<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Model_Resource_Cms_Page_Version extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialization
     */
    protected function _construct()
    {
        $this->_init('bubble_cmstree/cms_page_version', 'version_id');
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return $this
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        /*
         * For two attributes which represent timestamp data in DB
         * we should make converting such as:
         * If they are empty we need to convert them into DB
         * type NULL so in DB they will be empty and not some default value
         */
        foreach (array('custom_theme_from', 'custom_theme_to') as $field) {
            $value = !$object->getData($field) ? null : $object->getData($field);
            $object->setData($field, $this->formatDate($value));
        }

        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * @param Varien_Object $page
     * @return bool|int
     */
    public function deletePageDrafts(Varien_Object $page)
    {
        if ($page->getId()) {
            $adapter = $this->_getWriteAdapter();
            $where = array(
                'is_draft = ?' => 1,
                'page_id = ?' => $page->getId()
            );

            return $adapter->delete($this->getMainTable(), $where);
        }

        return true;
    }
}
<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition End User License Agreement
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magento.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license http://www.magento.com/license/enterprise-edition
 */

/**
 *
 * Start Date attribute backend
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magebuzz_Bannerads_Model_Attribute_Backend_Startdate extends Mage_Catalog_Model_Product_Attribute_Backend_Startdate
{
   /**
    * Get attribute value for save.
    *
    * @param Varien_Object $object
    * @return string|bool
    */
    protected function _getValueForSave($object)
    {
        $attributeName  = $this->getAttribute()->getName();
        $startDate      = $object->getData($attributeName);
        if ($startDate === false) {
            return false;
        }
        return $startDate;
    }

   /**
    * Before save hook.
    * Prepare attribute value for save
    *
    * @param Varien_Object $object
    * @return Mage_Catalog_Model_Product_Attribute_Backend_Startdate
    */
    public function beforeSave($object)
    {
        $startDate = $this->_getValueForSave($object);
        if ($startDate === false) {
            return $this;
        }
        parent::beforeSave($object);
        return $this;
    }

   /**
    * Product from date attribute validate function.
    * In case invalid data throws exception.
    *
    * @param Varien_Object $object
    * @throws Mage_Eav_Model_Entity_Attribute_Exception
    * @return bool
    */
    public function validate($object)
    {
        $attr      = $this->getAttribute();
        $maxDate   = $attr->getMaxValue();
        $startDate = $this->_getValueForSave($object);
        if ($startDate === false) {
            return true;
        }

        if ($maxDate) {
            $date     = Mage::getModel('core/date');
            $value    = $date->timestamp($startDate);
            $maxValue = $date->timestamp($maxDate);

            if ($value > $maxValue) {
                $message = Mage::helper('catalog')->__('The From Date value should be less than or equal to the To Date value.');
                $eavExc  = new Mage_Eav_Model_Entity_Attribute_Exception($message);
                $eavExc->setAttributeCode($attr->getName());
                throw $eavExc;
            }
        }
        return true;
    }

    public function formatDate($date)
    {
      if (empty($date)) {
        return null;
      }
      // unix timestamp given - simply instantiate date object
      if (preg_match('/^[0-9]+$/', $date)) {
        $date = new Zend_Date((int)$date);
      }
      // international format
      else if (preg_match('#^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$#', $date)) {
        $zendDate = new Zend_Date();
        $date = $zendDate->setIso($date);
      }
      // parse this date in current locale, do not apply GMT offset
      else {
        $date = Mage::app()->getLocale()->date($date,
          Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
          null, false
        );
      }
      return $date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    }
}

<?php
/**
 * @copyright   Copyright (c) 2013 AZeBiz Co. LTD
 */
class Magebuzz_Faq_Model_Categorygroup extends Varien_Object {
  const ONLINE_FAQ	= 1;
  const IN_STORE_FAQ	= 2;
  const ABOUT_INDEX	= 3;

  static public function getOptionArray()
  {
    return array(
      self::ONLINE_FAQ    => Mage::helper('faq')->__('Online FAQ'),
      self::IN_STORE_FAQ   => Mage::helper('faq')->__('In-store FAQ'),
      self::ABOUT_INDEX   => Mage::helper('faq')->__('About Index Living Mall')
    );
  }
}
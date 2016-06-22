<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
class Amasty_Smtp_Model_System_Config_Source_Security
{
    public function toOptionArray()
    {
        return array(
            array('value' => Amasty_Smtp_Helper_Security::AMASTY_SECURITY_NONE,
                  'label' => Mage::helper('amsmtp')->__('None')),
            array('value' => Amasty_Smtp_Helper_Security::AMASTY_SECURITY_SSL,
                  'label' => Mage::helper('amsmtp')->__('SSL')),
            array('value' => Amasty_Smtp_Helper_Security::AMASTY_SECURITY_TLS,
                  'label' => Mage::helper('amsmtp')->__('TLS')),
        );
    }
}
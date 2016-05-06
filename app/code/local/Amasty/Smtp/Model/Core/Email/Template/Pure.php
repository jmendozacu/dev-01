<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
if (Mage::helper('core')->isModuleEnabled('Amasty_Perm')) {
    class Amasty_Smtp_Model_Core_Email_Template_Pure extends Amasty_Perm_Model_Core_Email_Template {}
} elseif (Mage::helper('core')->isModuleEnabled('Amasty_Customerattr')) {
    class Amasty_Smtp_Model_Core_Email_Template_Pure extends Amasty_Customerattr_Model_Rewrite_Core_Email_Template {}
} else {
    class Amasty_Smtp_Model_Core_Email_Template_Pure extends Mage_Core_Model_Email_Template {}
}
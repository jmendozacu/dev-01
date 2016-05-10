<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
if (Mage::helper('core')->isModuleEnabled('Amasty_Orderattach')) {
    class Amasty_Orderattr_Model_Sales_Order_Pure extends Amasty_Orderattach_Model_Sales_Order {}
} elseif (Mage::helper('core')->isModuleEnabled('Amasty_Deliverydate')) {
    class Amasty_Orderattr_Model_Sales_Order_Pure extends Amasty_Deliverydate_Model_Sales_Order {}
} elseif (Mage::helper('core')->isModuleEnabled('AdjustWare_Deliverydate')) {
    class Amasty_Orderattr_Model_Sales_Order_Pure extends AdjustWare_Deliverydate_Model_Rewrite_FrontSalesOrder {}
} else {
    class Amasty_Orderattr_Model_Sales_Order_Pure extends Mage_Sales_Model_Order {}
}
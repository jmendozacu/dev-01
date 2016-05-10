<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
class Amasty_Orderattr_Model_Source_Group
{
    public function toOptionArray()
    {
        return Mage::getResourceModel('customer/group_collection')->load()->toOptionArray();
    }
}

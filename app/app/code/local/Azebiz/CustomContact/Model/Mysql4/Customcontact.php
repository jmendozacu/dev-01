<?php
class Azebiz_CustomContact_Model_Mysql4_Customcontact extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("customcontact/customcontact", "contact_id");
    }
}
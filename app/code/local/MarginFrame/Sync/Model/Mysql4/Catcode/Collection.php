<?php

class MarginFrame_Sync_Model_Mysql4_Catcode_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('mgfsync/catcode');
    }
}
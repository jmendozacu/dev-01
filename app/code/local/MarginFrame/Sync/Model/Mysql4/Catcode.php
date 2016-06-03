<?php

class MarginFrame_Sync_Model_Mysql4_Catcode extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('mgfsync/catcode', 'id');
    }
}
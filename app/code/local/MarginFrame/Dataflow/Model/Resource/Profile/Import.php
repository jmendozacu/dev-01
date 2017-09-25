<?php

class MarginFrame_Dataflow_Model_Resource_Profile_Import
    extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('marginframe_dataflow/profile_import', 'entity_id');
    }
}

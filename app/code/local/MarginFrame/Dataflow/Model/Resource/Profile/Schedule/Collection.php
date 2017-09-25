<?php

class MarginFrame_Dataflow_Model_Resource_Profile_Schedule_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('marginframe_dataflow/profile_schedule');
    }

    public function joinProfileInformation()
    {
        $this->join(
            array('profile' => 'marginframe_dataflow/profile_import'),
            'profile_id=entity_id',
            array('profile_name' => 'name')
        );

        return $this;
    }
}

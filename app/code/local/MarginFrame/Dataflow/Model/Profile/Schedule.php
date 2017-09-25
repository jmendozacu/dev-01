<?php

class MarginFrame_Dataflow_Model_Profile_Schedule
    extends Mage_Cron_Model_Schedule
{
    /**
     * Class constructor
     */
    public function _construct()
    {
        $this->_init('marginframe_dataflow/profile_schedule');
    }
}
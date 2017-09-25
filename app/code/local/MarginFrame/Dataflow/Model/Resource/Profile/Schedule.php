<?php

class MarginFrame_Dataflow_Model_Resource_Profile_Schedule
    extends Mage_Cron_Model_Resource_Schedule
{
    public function _construct()
    {
        $this->_init('marginframe_dataflow/profile_schedule', 'schedule_id');
    }

    /**
     * If job is currently in $currentStatus, set it to $newStatus
     * and return true. Otherwise, return false and do not change the job.
     * This method is used to implement locking for cron jobs.
     *
     * @param unknown_type $scheduleId
     * @param String $newStatus
     * @param String $currentStatus
     * @return unknown
     */
    public function trySetJobStatusAtomic($scheduleId, $newStatus, $currentStatus)
    {
        $write = $this->_getWriteAdapter();
        $result = $write->update(
            $this->getMainTable(),
            array('status' => $newStatus),
            array('schedule_id = ?' => $scheduleId, 'status = ?' => $currentStatus)
        );
        if ($result == 1) {
            return true;
        }
        return false;
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
class Amasty_Smtp_Model_Resource_Log extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('amsmtp/log', 'log_id');
    }

    public function truncate()
    {
        $connection = $this->_getWriteAdapter();
        $connection->query('TRUNCATE TABLE ' . $this->getTable('amsmtp/log'));
    }

    public function clear($days)
    {
        $connection = $this->_getWriteAdapter();
        $sql = 'DELETE FROM ' . $this->getTable('amsmtp/log') .
                    ' WHERE cdate < "' . date('Y-m-d H:i:s', strtotime('-' . +$days . ' day')) . '"';
        $connection->query($sql);
    }
}
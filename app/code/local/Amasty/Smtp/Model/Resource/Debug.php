<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
class Amasty_Smtp_Model_Resource_Debug extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('amsmtp/debug', 'debug_id');
    }

    public function truncate()
    {
        $connection = $this->_getWriteAdapter();
        $connection->query('TRUNCATE TABLE ' . $this->getTable('amsmtp/debug'));
    }

    public function clear($days)
    {
        $connection = $this->_getWriteAdapter();
        $sql = 'DELETE FROM ' . $this->getTable('amsmtp/debug') .
                    ' WHERE cdate < "' . date('Y-m-d H:i:s', strtotime('-' . +$days . ' day')) . '"';
        $connection->query($sql);
    }
}
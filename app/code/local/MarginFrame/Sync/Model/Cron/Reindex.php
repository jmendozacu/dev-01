<?php
class MarginFrame_Sync_Model_Cron_Reindex extends Mage_Core_Model_Abstract
{
	
	public function Run() {
		Mage::helper('mgfsync/data')->reindex();
   	}
}
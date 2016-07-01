<?php
class MarginFrame_Sync_Model_Cron_Reindex extends Mage_Core_Model_Abstract
{
	
	public function Run() {
		$check = false;
		$message[] = array();
		try{
			$processes = Mage::getSingleton('index/process')->getCollection();
			$temp = array();
			foreach ($processes as $index) {
				$index->reindexAll();
			}
			//Flush all cache				
			Mage::app()->getCacheInstance()->flush();
		} catch(Exception $ex) {
			$check = true;
			$message[] = 'Error : '.$ex->getMessage();
		}
		$sync_type = 'Reindex';
		Mage::helper('mgfsync/data')->logSync($check, $sync_type, $message, '--');
   	}
}
<?php
class MarginFrame_Sync_Model_Cron_RenameECOM extends Mage_Core_Model_Abstract
{
	
	public function Run() {
		$message = array();
		$check = false ;
		try {

			$dirs = array();
			$dirs[] = Mage::getBaseDir('var').DS.'export'.DS.'product_active'.DS;
			$dirs[] = Mage::getBaseDir('var').DS.'export'.DS.'product_master'.DS;
			// Tiw

			// Create folder
			foreach ($dirs as $dir) {
				if (!file_exists($dir)) {
					$file = new Varien_Io_File();
					$file->mkdir($dir);
					// $file->mkdir($dir.'/save');
				}	

				$dh  = opendir($dir);
				while (false !== ($filename = readdir($dh))) {
					$files[] = $filename;

					if(strrpos(strtoupper($filename), '.TXT') ) {
						$newfile = explode('.', $filename);
						$date = date('Ymd');
						if(strlen($newfile[0]) > 8){
							$laststring = substr($newfile[0], -8);
							if (date('Ymd', strtotime($laststring)) != $laststring){
								rename($dir.$filename, $dir.$newfile[0].$date.'.TXT');
							}
						} else {
							rename($dir.$filename, $dir.$newfile[0].$date.'.TXT');
						}
						
					}
				}

			}

			$log = 'Success';

		} catch(Exception $ex) {
			$check = true;
			$message[] = 'Error : '.$ex->getMessage();
		}

		// $sync_type = 'Rename ECOM';
		// $filenamecsv = '';
		// Mage::getHelper('mgfsync/data')->logSync($check, $sync_type, $message, $filenamecsv);

   	}
}
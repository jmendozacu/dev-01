<?php
class MarginFrame_Sync_Model_Cron_Productstatus extends Mage_Core_Model_Abstract
{
	
	public function Run() {
		// /var/interface/product_status
		$message = array();
		$check = false ;
		$filenamecsv ='';

		try {
			
			$dir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'product_status'.DS;
			$dirprepare = $dir.'prepare'.DS;

			$filename_log = "mgfsync_productstatus.log";
			// Tiw
			// Create folder
			if (!file_exists($dir)) {
				$file = new Varien_Io_File();
				$file->mkdir($dir);
			}

			//create prepare for fast bluk import
			if (!file_exists($dirprepare)) {

				$file_prepare = new Varien_Io_File();
				$file_prepare->mkdir($dirprepare);
			} 

			$dh  = opendir($dir);
			$sourceModel = Mage::getModel('catalog/product')->getResource()
    			->getAttribute('price_tag')->getSource();
    		
			$dataImport[] = implode(',', array(
				'sku',
				'status'
			));
			while (false !== ($filename = readdir($dh))) {
			    $files[] = $filename;

			    // find .trg file
			    if(strrpos($filename, '.trg')) {

			    	// rename file .trg to .csv
			    	$filenamecsv = str_replace('.trg', '.txt', $filename);

					$row = 1;
					// $content = file_get_contents($dir.$filenamecsv);

					// $content = iconv('UTF-16LE', 'UTF-8', $content);
					// $content = iconv('ASCII//TRANSLIT', 'UTF-8', $content);
					// $content = preg_replace('~\R~u', "\r\n", $content);
					// $content = explode("\r\n", $content);

					if (($handle = fopen($dir.$filenamecsv, "r")) !== FALSE) {
						$csvdata = array();
						while (($data = fgetcsv($handle, 1000, "|")) !== FALSE) {
							if($row==1){
					        	$row++;
					        	continue;
					        }
					        $sku = trim($data[0]);
					        $csvdata[$sku]['discontinue'] = trim($data[1]);
						}	
					}

					Mage::log('close file : '.$dir.$filenamecsv, null, $filename_log,true);
					$rowdata = array();
				    foreach ($csvdata as $sku => $data) {
				    	// $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
				    	$rowdata = array();
				    	$rowdata[0] = $sku;	
				    	$discontinue = $data['discontinue'] == 0 ? 1:2;
				    	$rowdata[1] = $discontinue;
				    	$dataImport[] = implode(',',$rowdata);
				    }
				    $temp = implode("\n", $dataImport);
			    	file_put_contents($dirprepare."Update_Productstatus.csv", $temp);

					// moved file to completed path
					$newdir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'product_status'.DS.'save'.DS;

					// Tiw
					// Create folder
					if (!file_exists($newdir)) {
						$file = new Varien_Io_File();
						$file->mkdir($newdir);
					}

					// Mage::log($newdir);
					unlink($dir.$filename);
					rename($dir.$filenamecsv, $newdir.$filenamecsv);

					// Tiw
					// check to remove file
					if (!file_exists($dir.$filename)) {
						Mage::log('removed : '.$dir.$filename, null, $filename_log,true);
					}else{
						Mage::log('can not removed : '.$dir.$filename, null, $filename_log,true);
					}

					// check to move file
					if (!file_exists($dir.$filenamecsv)) {
						Mage::log('moved to completed : '.$newdir.$filenamecsv, null, $filename_log,true);
					}else{
						Mage::log('can not moved : '.$newdir.$filenamecsv, null, $filename_log,true);
					}
			    }
			}

		} catch(Exception $ex) {
			$check = true;
			$message[] = 'Error : '.$ex->getMessage();
		}

		$sync_type = 'Product Status';
		Mage::helper('mgfsync/data')->logSync($check, $sync_type, $message, $filenamecsv);

   	}
}
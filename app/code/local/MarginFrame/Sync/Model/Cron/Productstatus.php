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

			$filename_log = "mgfsync_productstatus.log";
			// Tiw
			// Create folder
			if (!file_exists($dir)) {
				$file = new Varien_Io_File();
				$file->mkdir($dir);
			}

			$dh  = opendir($dir);

			while (false !== ($filename = readdir($dh))) {
			    $files[] = $filename;

			    // find .trg file
			    if(strrpos($filename, '.trg')) {

			    	// rename file .trg to .csv
			    	$filenamecsv = str_replace('.trg', '.txt', $filename);

					$row = 1;

					if (($handle = fopen($dir.$filenamecsv, "r")) !== FALSE) {
					    Mage::log('=========================================================', null, $filename_log);
					    Mage::log('open file : '.$dir.$filenamecsv, null, $filename_log);

					    $csvdata = array();
					    while (($data = fgetcsv($handle, 1000, "|")) !== FALSE) {
					    	//skip header row
					        if($data[0] == 'Article'){
					        	continue;
					        }
					        $sku = trim($data[0]);
					        $csvdata[$sku]['new'] = trim($data[1]);
					        $csvdata[$sku]['discontinue'] = trim($data[2]);
					        $csvdata[$sku]['dontmiss'] = trim($data[3]);
					    }

					    $storeId = 0;
					   
			    		
			    		$product = null;
			    		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
					    foreach ($csvdata as $sku => $data) {
					    	$product = Mage::getModel('catalog/product')
					    	->loadByAttribute('sku', $sku);
					    	if ($product->getId()) {
					    		Mage::log('found sku : '.$sku, null, $filename_log);
					    		
					    		$product->setData('new', $data['new']);

					    		$discontinue = $data['discontinue'] == 0 ? 1:2;
					    		$product->setData('status', $discontinue);
					    		$product->setData('dontmiss', $data['dontmiss']);
					    		

					    		// call save() method to save your product with updated data
								try{
									$product->save();
									// $log = 'Success';
								} catch (Exception $ex) {
									$check = true;
									$message[] = 'error sku : '.$sku.'-'.$ex->getMessage();
									// handle the error here!!
									Mage::log('error sku : '.$sku.'-'.$ex->getMessage(), null, $filename_log);
								}
								
					    		
					    	} else {
								 Mage::log("SKU not found : ".$sku, null, $filename_log);
							}
							
					    	
					    }
					    unset($product);
						fclose($handle);
						
						Mage::log('close file : '.$dir.$filenamecsv, null, $filename_log);

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
							Mage::log('removed : '.$dir.$filename, null, $filename_log);
						}else{
							Mage::log('can not removed : '.$dir.$filename, null, $filename_log);
						}

						// check to move file
						if (!file_exists($dir.$filenamecsv)) {
							Mage::log('moved to completed : '.$newdir.$filenamecsv, null, $filename_log);
						}else{
							Mage::log('can not moved : '.$newdir.$filenamecsv, null, $filename_log);
						}
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
<?php
class MarginFrame_Sync_Model_Cron_Price extends Mage_Core_Model_Abstract
{
	
	public function Run() {
		// /var/interface/stock
		$message = array();
		$check = false ;
		try {

			$dir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'price'.DS;

			$filename_log = "mgfsync_price.log";
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

					        $sku = trim($data[0]);
					        $csvdata[$sku]['price'] = trim($data[1]);
					        $csvdata[$sku]['special_price'] = trim($data[2]);
					        $csvdata[$sku]['special_from_date'] = trim($data[3]);
					        $csvdata[$sku]['special_to_date'] = trim($data[4]);
					    }

					    $storeId = 0;
					   
			    		
			    		$product = null;
			    		
			    		
			    		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
					    foreach ($csvdata as $sku => $data) {
					    	$product = Mage::getModel('catalog/product')
					    	->loadByAttribute('sku', $sku);
					    	if ($product->getId()) {
					    		Mage::log('found sku : '.$sku, null, $filename_log);

					    		if ($data['price'] != 'Unset') {
					    			$product->setPrice(number_format($data['price'],2));

					    			if (strtolower($data['special_price']) != 'unset') {
						    			$product->setSpecialPrice(number_format($data['special_price'],2));

						    			$product->setSpecialFromDate(date('Y-m-d',strtotime($data['special_from_date'])));
										$product->setSpecialFromDateIsFormated(true);

										$product->setSpecialToDate(date('Y-m-d',strtotime($data['special_to_date'])));
										$product->setSpecialToDateIsFormated(true);
						    		}else{
						    			$product->setSpecialPrice(0);
						    			
						    			$product->setSpecialFromDate(false);
										$product->setSpecialFromDateIsFormated(true);

										$product->setSpecialToDate(false);
										$product->setSpecialToDateIsFormated(true);
						    		}
					    		}else{
					    			$product->setStatus(2);
					    		}

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
						$newdir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'price'.DS.'save'.DS;

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
			$log = 'Error : '.$ex->getMessage();
		}

		$resource = Mage::getSingleton('core/resource');
    	$readConnection = $resource->getConnection('core_read');
    	$writeConnection = $resource->getConnection('core_write');

	    //
	    if($check){
	    	//success
	    	 $query = "
			    	INSERT INTO `tbl_sync_log` (sync_type, created_at, log,message,filename)
			    	VALUES ('Retail Price', NOW(), 'Success','','$filenamecsv)')
			    ";
	    } else {
	    	//fail 
	    	$query = "
			    	INSERT INTO `tbl_sync_log` (sync_type, created_at, log,message,filename)
			    	VALUES ('Retail Price', NOW(), 'Fail','".json_encode($message)."','$filenamecsv)')
			    ";
	    }
	    $writeConnection->query($query);

   	}
}
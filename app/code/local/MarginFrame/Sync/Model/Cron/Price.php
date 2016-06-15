<?php
class MarginFrame_Sync_Model_Cron_Price extends Mage_Core_Model_Abstract
{
	
	public function Run() {
		// /var/interface/price
		$message = array();
		$check = false ;
		$filenamecsv = '';
		try {

			$dir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'retail_price'.DS;

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
					    Mage::log('=========================================================', null, $filename_log,true);
					    Mage::log('open file : '.$dir.$filenamecsv, null, $filename_log,true);

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

			    		$processes = Mage::getSingleton('index/process')->getCollection();
						$temp = array();
			    		foreach ($processes as $key => $value) {
							$temp[$value->getProcessId()] = $value->getMode();
							$value->setData('mode',Mage_Index_Model_Process::MODE_MANUAL)->save();
						}
			    		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
					    foreach ($csvdata as $sku => $data) {
					    	try{
						    	$product = Mage::getModel('catalog/product')
						    	->loadByAttribute('sku', $sku);
						    	if ($product) {
						    		Mage::log('found sku : '.$sku, null, $filename_log,true);
						    		if ($data['price'] != 'Unset') {
						    			$product->setPrice(number_format($data['price'],2));

						    			if (strtolower($data['special_price']) != 'unset') {
							    			$product->setSpecialPrice(number_format($data['special_price'],2));

							    			if (strtolower($data['special_from_date']) != 'unset') {
						    					$product->setSpecialFromDate(date('Y-m-d',strtotime($data['special_from_date'])));
												$product->setSpecialFromDateIsFormated(true);
						    				}

						    				if (strtolower($data['special_to_date']) != 'unset') {
						    					$product->setSpecialToDate(date('Y-m-d',strtotime($data['special_to_date'])));
												$product->setSpecialToDateIsFormated(true);
						    				}
							    		}else{
							    			$product->setSpecialPrice(null);
							    			
							    			$product->setSpecialFromDate(false);
											$product->setSpecialFromDateIsFormated(true);

											$product->setSpecialToDate(false);
											$product->setSpecialToDateIsFormated(true);
							    		}
						    		}else{
						    			if (strtolower($data['special_price']) != 'unset' and $data['special_price'] != '0') {
						    				$product->setPrice(number_format($data['special_price'],2));
						    				$product->setSpecialPrice(number_format($data['special_price'],2));

						    				if (strtolower($data['special_from_date']) != 'unset') {
						    					$product->setSpecialFromDate(date('Y-m-d',strtotime($data['special_from_date'])));
												$product->setSpecialFromDateIsFormated(true);
						    				}

						    				if (strtolower($data['special_to_date']) != 'unset') {
						    					$product->setSpecialToDate(date('Y-m-d',strtotime($data['special_to_date'])));
												$product->setSpecialToDateIsFormated(true);
						    				}
						    			} else {
						    				$product->setVisibility(1);
						    				$product->setStatus(2);
						    			}

						    		}

						    		// call save() method to save your product with updated data
										$product->save();
										// $log = 'Success';
						    	} else {
									 Mage::log("SKU not found : ".$sku, null, $filename_log,true);
								}
							} catch (Exception $ex) {
								$check = true;
								$message[] = 'error sku : '.$sku.'-'.$ex->getMessage();
								// handle the error here!!
								Mage::log('error sku : '.$sku.'-'.$ex->getMessage(), null, $filename_log,true);
								
							}
					    	
					    }
					    foreach ($temp as $key => $mode) {
							$process = Mage::getSingleton('index/process')->load($key);
							$process->setData('mode',Mage_Index_Model_Process::MODE_REAL_TIME)->save();
						}
						// Mage::helper('mgfsync/data')->reindex();
					    unset($product);
						fclose($handle);
						
						Mage::log('close file : '.$dir.$filenamecsv, null, $filename_log,true);

						// moved file to completed path
						$newdir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'retail_price'.DS.'save'.DS;

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
			   
			}

		} catch(Exception $ex) {
			$check = true;
			$message[] = 'Error : '.$ex->getMessage();
		}

		$sync_type = 'Retail Price';
		Mage::helper('mgfsync/data')->logSync($check, $sync_type, $message, $filenamecsv);

   	}
}
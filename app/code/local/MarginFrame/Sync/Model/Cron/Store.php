<?php
class MarginFrame_Sync_Model_Cron_Store extends Mage_Core_Model_Abstract
{
	
	public function Run() {
		// /var/interface/store
		$message = array();
		$check = false ;
		$filenamecsv = '';
		try {
			
			$dir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'store'.DS;

			$filename_log = "mgfsync_store.log";
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
					        //$result[] = $data;
					        //skip header row
					        // if($data[0] == 'ARTICLE' || $data[1] == 'Warehouse' || $data[2] == 'QTY'){
					        // 	continue;
					        // }

					        $csvdata[$data[0]][$data[1]] = trim($data[2]);

					    }

					  	// prepare to load products
					    $product = Mage::getModel('catalog/product');
					    $dealerAdds = array();
					    $dealerDels = array();
					    $processes = Mage::getSingleton('index/process')->getCollection();
						$temp = array();
			    		foreach ($processes as $key => $value) {
							$temp[$value->getProcessId()] = $value->getMode();
							$value->setData('mode',Mage_Index_Model_Process::MODE_MANUAL)->save();
						}
					    foreach ($csvdata as $sku => $data) {

					    	$p = $product->loadByAttribute('sku', $sku);

					    	if ($p) {
					    	Mage::log('found sku : '.$sku, null, $filename_log,true);

					    		$productId = $p->getIdBySku($sku);
					    		
					    		foreach ($data as $store_code => $status) {				    			
						    		$productdealerModel = Mage::getModel('dealerlocator/productdealer');
						    		
									$dealerOlds = $productdealerModel->getCollection()
										->addFieldToFilter('product_id', $productId)
										->getColumnValues('dealer_id');

						    		$s = Mage::getModel('dealerlocator/dealerlocator')->getCollection()
						    			->addFieldToFilter('store_code', $store_code)
						    			->getColumnValues('dealerlocator_id');

						    		foreach ($s as $value) {
						    			if ($status == 0) {
						    				$dealerDels[] = $value;
						    			}elseif ($status == 1) {
						    				$dealerAdds[] = $value;
						    			}
						    		}

						    		if(!$dealerAdds) {
						    			$dealerAdds[] = 0;
						    		}

						    		if(!$dealerDels) {
						    			$dealerDels[] = 0;
						    		}
				 
					    		}
					    		
					    	} else {
								Mage::log("SKU not found : ".$sku, null, $filename_log,true);
							}
							
							$dealerAddsDiff = array_diff($dealerAdds, $dealerOlds);
						    $dealerDelsDiff = array_intersect($dealerDels, $dealerOlds);
						    
							// call save() method to save your product with updated data
							try {
								//delete product dealer
						        $productdealerIdDels = $productdealerModel->getCollection()
						          	->addFieldToFilter('product_id', $productId)
						          	->addFieldToFilter('dealer_id', array('in' => $dealerDelsDiff))
						          	->getColumnValues('productdealer_id');

						        if ($productdealerIdDels != null) {
							        foreach($productdealerIdDels as $productdealerIdDel){
							          	$productdealerModel->setId($productdealerIdDel)->delete();
							        }
							        Mage::log('disabled sku : '.$sku. 'in last store_code '.$store_code, null, $filename_log,true);
							    }

						        //add new dealer
						        if($dealerAddsDiff != null){
						          	foreach($dealerAddsDiff as $dealerAdd){
							            $dataForSave['product_id'] = $productId;
							            $dataForSave['dealer_id'] = $dealerAdd;
							            $dataForSave['display'] = 1;
							            $productdealerModel->setData($dataForSave);
							            $productdealerModel->save();
							            // $log = 'Success';
						          	}
						          	Mage::log('active sku : '.$sku. 'in last store_code '.$store_code, null, $filename_log,true);
						        }

							} catch (Exception $ex) {
								$check = true;
								$message[] = 'error sku : '.$sku.'or store_code '.$store_code.'-'.$ex->getMessage();
								// handle the error here!!
								Mage::log('error sku : '.$sku.'or store_code '.$store_code, null, $filename_log,true);
							}
							unset($dealerOlds);
							unset($dealerDels);
							unset($dealerAdds);
							unset($p);
							unset($s);
					    	
					    }					
					    foreach ($temp as $key => $mode) {
							$process = Mage::getSingleton('index/process')->load($key);
							$process->setData('mode',Mage_Index_Model_Process::MODE_REAL_TIME)->save();
						}
						
						fclose($handle);
						Mage::log('close file : '.$dir.$filenamecsv, null, $filename_log,true);

						// moved file to completed path
						$newdir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'store'.DS.'save'.DS;

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

		$sync_type = 'Store';
		Mage::helper('mgfsync/data')->logSync($check, $sync_type, $message, $filenamecsv);

   	}
}
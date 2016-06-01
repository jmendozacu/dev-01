<?php
class MarginFrame_Sync_Model_Cron_Store extends Mage_Core_Model_Abstract
{
	
	public function Run() {
		// /var/interface/stock
		$dir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'store'.DS;

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
				    Mage::log('=========================================================', null, 'mgfsync_store.log');
				    Mage::log('open file : '.$dir.$filenamecsv, null, 'mgfsync_store.log');

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

				    foreach ($csvdata as $sku => $data) {

				    	$p = $product->loadByAttribute('sku', $sku);

				    	if ($p) {
				    	Mage::log('found sku : '.$sku, null, 'mgfsync_store.log');

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
							Mage::log("SKU not found : ".$sku, null, 'mgfsync_store.log');
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
						        Mage::log('disabled sku : '.$sku. 'in last store_code '.$store_code, null, 'mgfsync_store.log');
						    }

					        //add new dealer
					        if($dealerAddsDiff != null){
					          	foreach($dealerAddsDiff as $dealerAdd){
						            $dataForSave['product_id'] = $productId;
						            $dataForSave['dealer_id'] = $dealerAdd;
						            $dataForSave['display'] = 1;
						            $productdealerModel->setData($dataForSave);
						            $productdealerModel->save();
					          	}
					          	Mage::log('active sku : '.$sku. 'in last store_code '.$store_code, null, 'mgfsync_store.log');
					        }

						} catch (Exception $ex) {
							// handle the error here!!
							Mage::log('error sku : '.$sku.'or store_code '.$store_code, null, 'mgfsync_store.log');
						}
						unset($dealerOlds);
						unset($dealerDels);
						unset($dealerAdds);
						unset($p);
						unset($s);
				    	
				    }					

					fclose($handle);
					Mage::log('close file : '.$dir.$filenamecsv, null, 'mgfsync_store.log');

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
						Mage::log('removed : '.$dir.$filename, null, 'mgfsync_store.log');
					}else{
						Mage::log('can not removed : '.$dir.$filename, null, 'mgfsync_store.log');
					}

					// check to move file
					if (!file_exists($dir.$filenamecsv)) {
						Mage::log('moved to completed : '.$newdir.$filenamecsv, null, 'mgfsync_store.log');
					}else{
						Mage::log('can not moved : '.$newdir.$filenamecsv, null, 'mgfsync_store.log');
					}
				}

		    }
		}

   	}
}
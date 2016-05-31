<?php
class MarginFrame_Sync_Model_Cron_Price extends Mage_Core_Model_Abstract
{
	
	public function Run() {
		// /var/interface/stock
		$dir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'price'.DS;

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
				    // Mage::log('=========================================================', null, 'mgfsync_store.log');
				    // Mage::log('open file : '.$dir.$filenamecsv, null, 'mgfsync_store.log');

				    $csvdata = array();
				    while (($data = fgetcsv($handle, 1000, "|")) !== FALSE) {
				        //$result[] = $data;
				        //skip header row
				        // if($data[0] == 'ARTICLE' || $data[1] == 'Warehouse' || $data[2] == 'QTY'){
				        // 	continue;
				        // }

				        $csvdata[trim($data[0])]['normal_price'] = trim($data[1]);
				        $csvdata[trim($data[0])]['special_price'] = trim($data[2]);
				        $csvdata[trim($data[0])]['start_date'] = trim($data[3]);
				        $csvdata[trim($data[0])]['end_date'] = trim($data[4]);

				    }

				  	// prepare to load products
				    $websiteId = Mage::app()->getStore()->getWebsiteId();
				    $storeId = Mage::app()->getStore()->getStoreId();

				    foreach ($csvdata as $sku => $item) {

				    	$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

				    	if ($product) {
				    	// Mage::log('found sku : '.$sku, null, 'mgfsync_store.log');

				    		// $productId = $p->getIdBySku($sku);
				    		// $product->load($productId);
				    		$product->setWebsiteId($websiteId);
        					$product->setStoreId($storeId);

				    		// Set Product price
				    		if ($item['normal_price'] != 'Unset') {
				    			$product->setPrice(floatval($item['normal_price']));

				    			if ($item['special_price'] != 'Unset') {
					    			$product->setSpecialPrice(floatval($item['special_price']));

					    			$product->setSpecialFromDate($item['start_date']);
									$product->setSpecialFromDateIsFormated(true);

									$product->setSpecialToDate($item['end_date']);
									$product->setSpecialToDateIsFormated(true);
					    		}else{
					    			$product->setSpecialPrice('');
					    			
					    			$product->setSpecialFromDate('');
									$product->setSpecialFromDateIsFormated(true);

									$product->setSpecialToDate('');
									$product->setSpecialToDateIsFormated(true);
					    		}

				    		}else{
				    			$product->setPrice('');
				    		}

				    		// call save() method to save your product with updated data
							try{
								$product->save();
							} catch (Exception $ex) {
								// handle the error here!!
								// Mage::log('error sku : '.$sku, null, 'mgfsync_stock.log');
							}
							unset($product);
				    		
				    	} else {
							// Mage::log("SKU not found : ".$sku, null, 'mgfsync_store.log');
						}
				    	
				    }

					fclose($handle);
					// Mage::log('close file : '.$dir.$filenamecsv, null, 'mgfsync_store.log');

					// moved file to completed path
					$newdir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'price'.DS.'save'.DS;

					// Tiw
					// Create folder
					// if (!file_exists($newdir)) {
					// 	$file = new Varien_Io_File();
					// 	$file->mkdir($newdir);
					// }

					// // Mage::log($newdir);
					// unlink($dir.$filename);
					// rename($dir.$filenamecsv, $newdir.$filenamecsv);

					// // Tiw
					// // check to remove file
					// if (!file_exists($dir.$filename)) {
					// 	Mage::log('removed : '.$dir.$filename, null, 'mgfsync_store.log');
					// }else{
					// 	Mage::log('can not removed : '.$dir.$filename, null, 'mgfsync_store.log');
					// }

					// // check to move file
					// if (!file_exists($dir.$filenamecsv)) {
					// 	Mage::log('moved to completed : '.$newdir.$filenamecsv, null, 'mgfsync_store.log');
					// }else{
					// 	Mage::log('can not moved : '.$newdir.$filenamecsv, null, 'mgfsync_store.log');
					// }
				}

		    }
		}

   	}
}
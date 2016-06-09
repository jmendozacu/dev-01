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
			$sourceModel = Mage::getModel('catalog/product')->getResource()
    			->getAttribute('price_tag')->getSource();
    		
			
			while (false !== ($filename = readdir($dh))) {
			    $files[] = $filename;

			    // find .trg file
			    if(strrpos($filename, '.trg')) {

			    	// rename file .trg to .csv
			    	$filenamecsv = str_replace('.trg', '.txt', $filename);

					$row = 1;
					$content = file_get_contents($dir.$filenamecsv);

					$content = iconv('UTF-16LE', 'UTF-8', $content);
					//$content = iconv('ASCII//TRANSLIT', 'UTF-8', $content);
					$content = preg_replace('~\R~u', "\r\n", $content);
					$content = explode("\r\n", $content);

					foreach ($content as $row) {	
					    Mage::log('=========================================================', null, $filename_log,true);
					    Mage::log('open file : '.$dir.$filenamecsv, null, $filename_log,true);

					    $cols = explode("|", $row);
					    foreach ($cols as $data) {
					    	//skip header row
					        if($row==1){
					        	$row++;
					        	continue;
					        }
					        $sku = trim($data[0]);
					        // $csvdata[$sku]['new'] = trim($data[1]);
					        $csvdata[$sku]['discontinue'] = trim($data[1]);
					        // $csvdata[$sku]['dontmiss'] = trim($data[3]);
					        // $csvdata[$sku]['joyprice'] = trim($data[4]);
					        // $csvdata[$sku]['hotprice'] = trim($data[5]);
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
					    	$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
					    		
					    	if ($product) {
					    		Mage::log('found sku : '.$sku, null, $filename_log,true);
					    		
					    		// $product->setData('new', $data['new']);

					    		$discontinue = $data['discontinue'] == 0 ? 1:2;
					    		$product->setData('status', $discontinue);

					    		// $valuesText = "";
					    		// if($data['dontmiss']=='1'){
					    		// 	$valuesText .=',dontmiss';
					    		// }

					    		// if($data['joyprice']=='1'){
					    		// 	$valuesText .=',joyprice';
					    		// }

					    		// if($data['hotprice']=='1'){
					    		// 	$valuesText .=',hotprice';
					    		// }

					    		// $valuesText = ltrim($valuesText,',');
					    		// $valuesText = explode(',', $valuesText);


					    		// $valuesIds = array_map(array($sourceModel, 'getOptionId'), $valuesText);
								// $product->setData('price_tag', $valuesIds);

					    		// call save() method to save your product with updated data
								try{
									$product->save();
									// $log = 'Success';
								} catch (Exception $ex) {
									$check = true;
									$message[] = 'error sku : '.$sku.'-'.$ex->getMessage();
									// handle the error here!!
									Mage::log('error sku : '.$sku.'-'.$ex->getMessage(), null, $filename_log,true);
								}
								
					    		
					    	} else {
					    		echo "SKU not found : ".$sku.'<br>';
								Mage::log("SKU not found : ".$sku, null, $filename_log,true);
							}
							
					    	
					    }

					    foreach ($temp as $key => $mode) {
							$process = Mage::getSingleton('index/process')->load($key);
							$process->setData('mode',$mode)->save();
						}
						Mage::helper('mgfsync/data')->reindex();

					    unset($product);
						fclose($handle);
						
						Mage::log('close file : '.$dir.$filenamecsv, null, $filename_log,true);

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
			}

		} catch(Exception $ex) {
			$check = true;
			$message[] = 'Error : '.$ex->getMessage();
		}

		$sync_type = 'Product Status';
		Mage::helper('mgfsync/data')->logSync($check, $sync_type, $message, $filenamecsv);

   	}
}
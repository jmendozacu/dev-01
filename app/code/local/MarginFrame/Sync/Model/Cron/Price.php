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
			$dirprepare = $dir.'prepare'.DS;

			$filename_log = "mgfsync_price.log";
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
			
			$dataImport[] = implode(',', array(
				'sku',
				'price',
				'special_price',
				'special_from_date',
				'special_to_date',
				'visibility',
				// 'status'
			));

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
					        $csvdata[$sku]['sku'] = trim($data[0]);
					        $csvdata[$sku]['price'] = trim($data[1]);
					        $csvdata[$sku]['special_price'] = trim($data[2]);
					        $csvdata[$sku]['special_from_date'] = trim($data[3]);
					        $csvdata[$sku]['special_to_date'] = trim($data[4]);

					    }
					    
					    //$file = fopen($dirprepare."Import_Price.csv","w+");

					    $storeId = 0;
			    		$product = null;

			   //  		$processes = Mage::getSingleton('index/process')->getCollection();
						// $temp = array();
			   //  		foreach ($processes as $key => $value) {
						// 	$temp[$value->getProcessId()] = $value->getMode();
						// 	$value->setData('mode',Mage_Index_Model_Process::MODE_MANUAL)->save();
						// }
			    		// Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			    		// fputcsv($file,$dataImport);
					    foreach ($csvdata as $sku => $data) {
					    	$row = array();
					    	$row[0] = $sku;
					    	if (strtolower($data['price']) != 'unset') {
					    		$row[1] = number_format($data['price'],2,'.','');

				    			if (strtolower($data['special_price']) != 'unset') {
					    			$row[2] = number_format($data['special_price'],2,'.','');

					    			if (strtolower($data['special_from_date']) != 'unset') {
										$row[3] = date('Y-m-d',strtotime($data['special_from_date']));
				    				} else {
				    					$row[3] ='unset';
				    				}

				    				if (strtolower($data['special_to_date']) != 'unset') {
										$row[4] = date('Y-m-d',strtotime($data['special_to_date']));
				    				} else {
				    					$row[4]='unset';
				    				}

					    		}else{
					    			$row[2] = 'unset';
					    			$row[3] = 'unset';
					    			$row[4] = 'unset';
					    			// $row[5] = 'unset';
					    		}
					    		// $row[5] = '4';
				    			// $row[6] = '1';
				    		}else{
				    			// 110018247         |Unset        |Unset        |Unset   |Unset
				    			if (strtolower($data['special_price']) != 'unset' and $data['special_price'] != '0') {
				    				$row[1] = number_format($data['special_price'],2,'.','');
				    				$row[2] = number_format($data['special_price'],2,'.','');
				    				if (strtolower($data['special_from_date']) != 'unset') {
				    					$row[3] = date('Y-m-d',strtotime($data['special_from_date']));
				    				} else {
				    					$row[3] ='unset';
				    				}

				    				if (strtolower($data['special_to_date']) != 'unset') {
				    					$row[4] = date('Y-m-d',strtotime($data['special_to_date']));
				    				} else {
				    					$row[4] ='unset';
				    				}
				    			} else {
				    				$row[1] = 'unset';
				    				$row[2] = 'unset';
					    			$row[3] = 'unset';
					    			$row[4] = 'unset';
				    			}
				    		}
				    		if($row[1] > 0){
					    		$row[5] = '4';
				    		} else {
				    			$row[5] = '1';
				    		} 

				    		$dataImport[] = implode(',',$row);
				    		// fputcsv($file,$row);
				    	}
				    	$temp = implode("\n", $dataImport);
				    	file_put_contents($dirprepare."Import_Price.csv",$temp);
						
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
<?php
class MarginFrame_TTF_Model_Cron_Pim_Import extends Mage_Core_Model_Abstract
{

	protected $_downpath;
	protected $_savepath;
	protected $_temppath;
	
	public function __construct()
	{
		$storeId = Mage::app()->getStore()->getId();
		$this->_downpath = trim(Mage::getStoreConfig('ttfpim/import/downpath', $storeId));
		$this->_savepath = trim(Mage::getStoreConfig('ttfpim/import/savepath', $storeId));
		$this->_temppath = trim(Mage::getStoreConfig('ttfpim/import/dbtmppath', $storeId));
		$this->_downpath = rtrim($this->_downpath, DS);
		$this->_savepath = rtrim($this->_savepath, DS);
		$this->_temppath = rtrim($this->_temppath, DS);
	}	
	
	public function Run() {
		
		//set_time_limit 1 hours
		set_time_limit(3600);

		$results = '';	
		$startimporttxt = PHP_EOL . "== PIM IMPORT start at: ".date('Y-m-d H:i:s') . PHP_EOL;

		$exportdir = $this->_downpath;
		$savedir = $this->_savepath;
		$tempdir = $this->_temppath;

		if(!is_dir($exportdir)) mkdir($exportdir, 0777, true);
		if(!is_dir($savedir)) mkdir($savedir, 0777, true);
		if(!empty($tempdir) && $tempdir != '/export' && !is_dir($tempdir)) mkdir($tempdir, 0777, true);

		//echo $exportdir;
		//echo $savedir;
		//echo $tempdir;

		$dirlist = scandir($exportdir);

		foreach ($dirlist as $filetrg) {
			
			//check .trg file
			if(strrpos($filetrg, '.trg')) {
				
				//set start time
				$start_time = date('Y-m-d H:i:s');

				$status = 'Success';
				$results = $startimporttxt . "=== START PROCESS:: found trg "  . $filetrg . PHP_EOL;
				
				$filezip = str_replace('.trg', '.zip', $filetrg);
				$filename = $exportdir.DS.$filezip;
				$extractdir = $exportdir.DS.trim(str_replace('.zip', '', $filezip));
				
				if(!is_dir($exportdir)) mkdir($exportdir, 0777, true);

				//echo $filezip;
				//echo $filename;
				//echo $tempdir;
				//echo $extractdir;
				
				if (file_exists($filename)) {
					
					$results .= date('Y-m-d H:i:s')." : found zip ".$filezip . PHP_EOL;
					
					$zip = new ZipArchive;
					$results .= date('Y-m-d H:i:s')." : start extract file at ".$extractdir. PHP_EOL;
					$reszip = $zip->open($filename);
					if ($reszip === true) {

						//extractTo destination directory
						$zip->extractTo($exportdir);
						$zip->close();

						$results .= date('Y-m-d H:i:s')." : completed extract file" . PHP_EOL;
						
						$locale = 'en_US';	
						Mage::getSingleton('adminhtml/session')->setLocale($locale);
						//locale_set_default('en-US');
						setlocale(LC_ALL, "en_US.UTF-8");
						//Mage::log(date('Y-m-d H:i:s')." : completed to set locale to en_US", null, 'mgf_import.log');
						
						//move to SAVE path after extract zip
						try {
							//rename($exportdir.DS.$filetrg, $savedir.DS.$filetrg);
							//rename($exportdir.DS.$filezip, $savedir.DS.$filezip);
						} catch (Exception $e) {
							$results .= date('Y-m-d H:i:s')." : ERROR:: can not move to SAVE " . $e->getMessage() . PHP_EOL;
							$status = 'Error';
							$this->sendEmail($filezip, $start_time, $results, 'Error');
							continue;
						}
						
						//try to put sleep for many import file 
						//sleep(15);
						
						//skip image file
						$importfilenames = array('product_en.csv', 'product_th.csv', 'image.csv');
						try {

							//check price ,special_price column
							$havepricecol = false;
							$havespricecol = false;

							$imdata = array();

							foreach($importfilenames as $importfilename) {
								$csvfilePath = $extractdir.DS.$importfilename;
								//Mage::log(date('Y-m-d H:i:s')." : checking file_exists : ".$csvfilePath, null, 'mgf_import.log');
								if(file_exists($csvfilePath)) {

									if($importfilename == 'product_en.csv'){
										if (($handle = fopen($csvfilePath, "r")) !== FALSE) {
										    // Mage::log('=========================================================', null, $filename_log,true);
										    // Mage::log('open file : '.$dir.$filenamecsv, null, $filename_log,true);

										    $csvdata = array();
										    $headers = fgetcsv($handle, 0, ",");
										    $headers_count = count($headers);
										    while (($row = fgetcsv($handle, 0, ",")) !== FALSE) {
										    	//var_dump($headers_count, count($row)); die;
										    	if($headers_count == count($row)){
										    		$csvdata[] = array_combine($headers, $row);
										    	}
										    }

										    //check price ,special_price column
										    if(in_array('price', $headers)){
										    	$havepricecol = true;
										    }
										    if(in_array('special_price', $headers)){
										    	$havespricecol = true;
										    }

										    foreach ($csvdata as $data) {
										    	
											    //fixed for GOLIVE
											    $data['dc_type'] = isset($data['dc_type']) ? $data['dc_type'] : 'S';
											    $data['status'] = isset($data['status']) ? $data['status'] : 'Enabled';
											    $price = (isset($data['price']) && $data['price'] > 0) ? $data['price'] : '\N';
											    $special_price = (isset($data['special_price']) && $data['special_price'] > 0) ? $data['special_price'] : '\N';

										    	$imrow = array(
													$data['sku'],
													$data['dc_type'] == 'X' ? 1 : 0,
													$data['status'] == 'Enabled' ? 1 : 0,
													$price,
													isset($data['gc_external_code']) ? $data['gc_external_code'] : '',
													isset($data['gc_external_sv_code']) ? $data['gc_external_sv_code'] : '',
													isset($data['dc_type']) ? $data['dc_type'] : 'S',
													isset($data['cost_center']) ? $data['cost_center'] : '',
													$special_price
													/*
													$data['cost_center'],
													$data['gc_external_code'],
													$data['gc_external_sv_code'],
													$data['gc_lead_time_fri'],
													$data['gc_lead_time_mon'],
													$data['gc_lead_time_sat'],
													$data['gc_lead_time_sun'],
													$data['gc_lead_time_thu'],
													$data['gc_lead_time_tue'],
													$data['gc_lead_time_wed'],
													$data['is_returnable'],
													$data['markup_price'],
													$data['name'],
													$data['size'],
													$data['dc_type'],
													$data['express_mode'],
													$data['package_dimension'],
													$data['price'],
													$data['weight'],
													$data['status'],
													$data['visibility'],
													$data['manage_stock'],
													$data['use_config_manage_stock'],
													$data['sku'],
													$data['store'],
													$data['store_id'],
													$data['gc_sv_status'],
													$data['gc_article_status'],
													$data['gc_lv_status'],
													$data['use_config_max_sale_qty']
													*/
												);
												$imdata[] = implode(',', $imrow);
										    }
										}
									}
									// else if($importfilename == 'product_th.csv'){
									// 	$fp_source = fopen($csvfilePath, "r");											
									// 	$first3 = fread($fp_source, 3); 
									// 	if ($first3 != b"\xEF\xBB\xBF") {
									// 		$fp_dest = fopen($csvfilePath.'_tmp', 'w');
									// 		fwrite($fp_dest, "\xEF\xBB\xBF");
									// 		rewind($fp_source);
									// 		while (!feof($fp_source)) {
									// 			$contents = fread($fp_source, 8192);
									// 			fwrite($fp_dest, $contents);
									// 		}
									// 		fclose($fp_source);
									// 		fclose($fp_dest);
									// 		//unlink($csvfilePath);
									// 		rename($csvfilePath,$csvfilePath.'_org');
									// 		rename($csvfilePath.'_tmp',$csvfilePath);
									// 	} else {
									// 		fclose($fp_source);
									// 	}
									// }

									
									$profile = Mage::getModel('marginframe_dataflow/profile_import')
										->getCollection()
										->addFieldToFilter('name', $importfilename)
										->getFirstItem();
									if($profile->getId()) {
										try
										{
											Mage::getSingleton('core/session')->getMessages(true);
											Mage::getSingleton('adminhtml/session')->getMessages(true);
																							
											$results .= date('Y-m-d H:i:s')." : START IMPORT:: " . $importfilename . PHP_EOL;
											$imagePath = $extractdir.DS.'image';
											$profile->run($csvfilePath, $imagePath, $extractdir);
											
											//fixed for GOLIVE
											/*
											$session = Mage::getSingleton('adminhtml/session');
											//var_dump($session->getMessages());exit;
											if($session->getMessages()){
												$inner_error = '';
												foreach ($session->getMessages()->getItems() as $message) {
													//var_dump($message->getText());
													if($message->getType() == Mage_Core_Model_Message::ERROR) { //|| $message->getType() == Mage_Core_Model_Message::NOTICE){
														$inner_error = $inner_error . PHP_EOL . $message->getText();
													}
												}
												if(!empty($inner_error)) {
													$results .= date('Y-m-d H:i:s')." : WARNING:: " . $inner_error . PHP_EOL;
													$status = 'Warning';
												}
											}
											*/
											$results .= date('Y-m-d H:i:s') . " : COMPLETED IMPORT:: " . $importfilename . PHP_EOL;
										}
										catch(Exception $e){
											$results .= date('Y-m-d H:i:s')." : ERROR:: " . $e->getMessage() . PHP_EOL;
											$status = 'Error';			
											break;
										}
									}
									else {
										$results .= date('Y-m-d H:i:s')." : ERROR:: import profile '$importfilename' not found!!" . PHP_EOL;
										$status = 'Error';
										break;
									}
								}
								else {
									$results .= date('Y-m-d H:i:s')." : ERROR:: '$csvfilePath' not exists in extract dir!!" . PHP_EOL;
									$status = 'Error';
									break;
								}
							}

							//update vendor 1 (CDT)
							/*
							$results .= date('Y-m-d H:i:s').' : START update CDT vendor' . PHP_EOL;
							if(!empty($imdata)) {
								$content = implode("\n", $imdata);		
								$dbtmppath = Mage::getStoreConfig('ttfpim/import/dbtmppath');
                                if($dbtmppath == '/export'){
                                    $tmpimportfilename = $dbtmppath.DS.'tmp_import_vendor_product.csv';
                                    $ftp = Mage::getModel('ttf/ftp');
                                    $ftp->connect('database');
                                    $ftp->write($tmpimportfilename, $content, 0);
                                }
                                else {
                                	$tmpimportfilename = $dbtmppath.DS.'tmp_import_vendor_product.csv';
                                    file_put_contents($tmpimportfilename, $content);
                                }

								//$tmpimportfilename = $extractdir.DS.'tmp_import_vendor_product.csv';
								//file_put_contents($tmpimportfilename, $content);

								$sql = "TRUNCATE TABLE tmp_import_vendor_product;";
								Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);

								$sql = "LOAD DATA INFILE '$tmpimportfilename' REPLACE INTO TABLE tmp_import_vendor_product 
										FIELDS TERMINATED BY ',' LINES TERMINATED BY '\\n' 
										(sku, backorders, status, vendor_price, gc_external_code, gc_external_sv_code, dc_type, cost_center, special_price); ";
								Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);
								////echo 'reloaded tmp table : '. date('H:i:s').PHP_EOL;

								$sql = "INSERT catalog_product_website
										(product_id, website_id) 
										SELECT e.entity_id, 0
										FROM tmp_import_vendor_product t 
										JOIN catalog_product_entity e ON e.sku = t.sku 
										ON DUPLICATE KEY UPDATE website_id=0";
								Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);

								$sql = "INSERT catalog_product_website
										(product_id, website_id) 
										SELECT e.entity_id, 1
										FROM tmp_import_vendor_product t 
										JOIN catalog_product_entity e ON e.sku = t.sku 
										ON DUPLICATE KEY UPDATE website_id=1";
								Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);

								$sql = "INSERT udropship_vendor_product
										(vendor_id, vendor_sku, product_id, priority, backorders, `status`, state, gc_external_code, gc_external_sv_code, dc_type, cost_center) 
										SELECT 1, t.sku, e.entity_id, 0, t.backorders, t.`status`, 'new', t.gc_external_code, t.gc_external_sv_code, 
											t.dc_type, t.cost_center
										FROM tmp_import_vendor_product t 
										JOIN catalog_product_entity e ON e.sku = t.sku 
										ON DUPLICATE KEY UPDATE backorders=t.backorders, `status`=t.`status`, gc_external_code=t.gc_external_code, gc_external_sv_code=t.gc_external_sv_code, dc_type=t.dc_type, cost_center=t.cost_center;";
								Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);

								//check price ,special_price column
								if($havepricecol){
									$sql = "INSERT udropship_vendor_product
										(vendor_id, product_id, vendor_price) 
										SELECT 1, e.entity_id, t.vendor_price
										FROM tmp_import_vendor_product t 
										JOIN catalog_product_entity e ON e.sku = t.sku
										WHERE t.vendor_price is not null
										ON DUPLICATE KEY UPDATE vendor_price=t.vendor_price;";
									Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);
								}

								if($havespricecol){
									$sql = "INSERT udropship_vendor_product
										(vendor_id, product_id, special_price) 
										SELECT 1, e.entity_id, t.special_price
										FROM tmp_import_vendor_product t 
										JOIN catalog_product_entity e ON e.sku = t.sku 
										ON DUPLICATE KEY UPDATE special_price=t.special_price;";
									Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);
								}
								
								$sql = "INSERT udropship_vendor_product_assoc
										(vendor_id, product_id, is_attribute, is_udmulti) 
										SELECT 1, e.entity_id, 0, 1
										FROM tmp_import_vendor_product t 
										JOIN catalog_product_entity e ON e.sku = t.sku 
										ON DUPLICATE KEY UPDATE is_attribute=0, is_udmulti=1;";
								Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);

								$sql = "INSERT INTO solrbridge_solrsearch_index_pim
										SELECT 1, null, e.entity_id, 1 
										FROM tmp_import_vendor_product t
										JOIN catalog_product_entity e ON e.sku = t.sku
										on DUPLICATE key UPDATE solrbridge_solrsearch_index_pim.changed = 1;";
								Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);

								$sql = "INSERT INTO solrbridge_solrsearch_index_pim
										SELECT 5, null, e.entity_id, 1 
										FROM tmp_import_vendor_product t
										JOIN catalog_product_entity e ON e.sku = t.sku
										on DUPLICATE key UPDATE solrbridge_solrsearch_index_pim.changed = 1;";
								Mage::getSingleton('core/resource')->getConnection('core_write')->query($sql);

							}
							
							$results .= date('Y-m-d H:i:s').' : COMPLETED update CDT vendor' . PHP_EOL;
							*/

							//delete extract dir
							$results .= date('Y-m-d H:i:s').' : START to delete extract dir' . PHP_EOL;
							foreach(glob($extractdir.DS.'image'.DS.'*') as $del){
								chmod($del, 0777);
								unlink($del);
							}
							chmod($extractdir.DS.'image', 0777);
							rmdir($extractdir.DS.'image');
							foreach(glob($extractdir.DS.'*') as $del){
								chmod($del, 0777);
								unlink($del);
							}
							rmdir($extractdir);

							$results .= date('Y-m-d H:i:s').' : COMPLETED to delete extract dir' . PHP_EOL;
							
						} catch (Exception $e) {
							$results .= date('Y-m-d H:i:s')." : ERROR:: when import profile : " . $e->getMessage() . PHP_EOL;
							$status = 'Error';
							$this->sendEmail($filezip, $start_time, $results, 'Error');
							continue;
						}
					} else {
						$results .= date('Y-m-d H:i:s')." : ERROR:: failed to open extract file!! : " . $filezip . PHP_EOL;
						$status = 'Error';
						$this->sendEmail($filezip, $start_time, $results, 'Error');
						continue;
					}
				}
				else {
					rename($exportdir.DS.$filetrg, $savedir.DS.$filetrg);
					$results .= date('Y-m-d H:i:s')." : ERROR:: failed read zip!! : " . $filezip . PHP_EOL;
					$status = 'Error';
					$this->sendEmail($filezip, $start_time, $results, 'Error');
					continue;
				}
				
				//MOVE TO SAVE
				rename($exportdir.DS.$filezip, $savedir.DS.$filezip);
				rename($exportdir.DS.$filetrg, $savedir.DS.$filetrg);

				$results .= '=== COMPLETED PROCESS:: ' . $filezip . "\r\n";
				$this->sendEmail($filezip, $start_time, $results, $status);

			}

		}


		/*
		catalog_product_attribute	Product Attributes
		catalog_product_price		Product Prices
		catalog_url					Catalog Url Rewrites
		catalog_product_flat		Product Flat Data
		catalog_category_flat		Category Flat Data
		catalog_category_product	Category Products
		catalogsearch_fulltext		Catalog Search Index
		cataloginventory_stock		Stock status	
		*/

		/*
		//$results .= "RE-INDEX START->".date('Y-m-d H:i:s')."..................\r\n";
		$allIndex = array(
				'catalog_product_attribute',
				//'catalog_product_price',
				//'catalog_url',
				//'catalog_product_flat',
				//'catalog_category_flat',
				//'catalog_category_product',
				//'catalogsearch_fulltext',
				//'cataloginventory_stock',
		);		
		foreach($allIndex as $index) {
			$process = Mage::getSingleton('index/indexer')->getProcessByCode($index);
			if ($process) {
				$process->reindexEverything();
			}
		}
		//$results .= "RE-INDEX FINISHED->".date('Y-m-d H:i:s')."..................\r\n";
		//Mage::log(date('Y-m-d H:i:s').' : re-index finished!!', null, 'mgf_import.log');
		*/
		
		//echo $results;
		
		return $results;
   	}
	
   	//const XML_PATH_EMAIL_RECIPIENT  = 'system/cron/error_email';
   	const XML_PATH_EMAIL_RECIPIENT  = 'ttfpim/import/email';
   	public function sendEmail($filezip, $start_time, $results, $status)
   	{

   		$this->saveLog($filezip, $start_time, $results, $status);
   		return true;
   		try {
   			$mail = Mage::getModel('core/email');
   			$subject = "PIM IMPORT: $status on " . $filezip;
   			/*
   			$subject = '';
   			if($is_error){
   				$subject = 'PIM IMPORT: Error ' . $filezip;
   			} else {
   				$subject = 'PIM IMPORT: Success ' . $filezip;
   			}
   			*/
   			$body = nl2br($results);
   			 
   			//$mail->setToName('sittisak.t');
   			//$mail->setToEmail('sittisak.t@marginframe.com');
   			
   			$mailsetting = Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);
   			$recipients = explode(";", $mailsetting);

   			//echo Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);
   			foreach ($recipients as $recipient) {
   			
	   			$mail->setToEmail(trim($recipient));
	   			$mail->setSubject($subject);
	   			$mail->setBody($body);
	   			$mail->setFromEmail('no-reply@cdiscount.co.th');
	   			$mail->setFromName("CDISCOUNT THAILAND");
	   			$mail->setType('html');
	   	
	   			try {
	   				$mail->send();
	   			}
	   			catch (Exception $e) {}
   			}
   			
   			//TEST echo results
   			echo $body;
   		}
   		catch (Exception $e) {
   			//Mage::getSingleton('core/session')->addError('Unable to send.');
   		}
   	}

   	public function saveLog($file_name, $start_time, $results, $status){

		$end_time = date('Y-m-d H:i:s');

        $query = "INSERT INTO tbl_log_pim
        (
            file_name,
            start_time,
            end_time,
            results,
            status
        )
        VALUES
        (
            :file_name,
            :start_time,
            :end_time,
            :results,
            :status
        );";

        $binds = array(
            'file_name' => $file_name,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'results' => $results,
            'status' => $status
        );
        
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $write->query($query, $binds);

        return $this;
    }

}
<?php
class MarginFrame_Sync_Model_Cron_Stock extends Mage_Core_Model_Abstract
{
	
	public function Run() {
		// /var/interface/stock
		$message = array();
		$check = false ;
		$filenamecsv='';
		try {
			
			$dir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'stock'.DS;
			$dirprepare = $dir.'prepare'.DS;

			$filename_log = "mgfsync_stock.log";
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

					        // array(
					        // 	'sku'=>array(
					        // 		'warehouse'=>'1100',
					        // 		'qty'=>'10'
					        // 	)
					        // )
					        $csvdata[$data[0]]['warehouse'] = trim($data[1]);
					        $csvdata[$data[0]]['qty'] = trim($data[2]);
					    }

					    $orderqty = array();

					    if(count($csvdata) > 0) {

					    	// get product_id, qty_ordered in state = new, pending_payment
							$collection = Mage::getResourceModel('sales/order_item_collection')
							    // ->addAttributeToSelect('*')
							    ->AddAttributeToSelect('product_id')
							    ->AddAttributeToSelect('sku')
							    ->AddAttributeToSelect('qty_ordered')
							;

							$collection
							    ->getSelect()
							    ->join(
							        array('orders'=> 'sales_flat_order'),
							        'orders.entity_id = main_table.order_id', 
							        array(
							            //'orders.customer_email',
							            //'orders.customer_id',
							            //'orders.state as order_state',
							            //'orders.status as order_status'
							        )
							    )
							;

							$collection
							    ->getSelect()
							    ->where("orders.state in ('new', 'pending_payment')")
							;

							$collection->load();

							// array(
							// 	'product_id-1' => '1',
							// 	'product_id-2' => '2',
							// )

							foreach ($collection->getData() as $item) {	
								if(isset($orderqty[$item['sku']])){
									$orderqty[$item['sku']] += number_format($item['qty_ordered']);
								} else {
									$orderqty[$item['sku']] = number_format($item['qty_ordered']);
								}
							}

					    }

					    $dataImport[] = implode(',', array(
							'sku',
							'qty',
							'is_in_stock'
						));

					    foreach ($csvdata as $sku => $item) {
					    	$qty = $item['qty'];
					    	$row = array();
					    	$sku = trim($sku);
					    	$row[0] = $sku;
     						if(isset($orderqty[$sku])){
     							$qty = $qty - $orderqty[$sku];
     						}
	     					$row[1] = $qty;
	     					if($qty > 0 ){
	     						$row[2] = '1';
	     					} else {
	     						$row[2] = '0';
	     					}
	     					$dataImport[] = implode(',',$row);
					    }
					    $temp = implode("\n", $dataImport);
					    file_put_contents($dirprepare."Import_Stock.csv",$temp);

						fclose($handle);
						Mage::log('close file : '.$dir.$filenamecsv, null, $filename_log,true);

						// moved file to completed path
						$newdir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'stock'.DS.'save'.DS;

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

		$sync_type = 'Stock';
		Mage::helper('mgfsync/data')->logSync($check, $sync_type, $message, $filenamecsv);

   	}
}
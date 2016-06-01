<?php
class MarginFrame_Sync_Model_Cron_Productmaster extends Mage_Core_Model_Abstract
{
	
	public function Run() {
		// /var/interface/stock
		$dir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'product_master'.DS;

		$filelogName = "mgfsync_productmaster.log";
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
				    Mage::log('=========================================================', null, $filelogName);
				    Mage::log('open file : '.$dir.$filenamecsv, null, $filelogName);

				    $csvdata = array();
				    while (($data = fgetcsv($handle, 1000, "|")) !== FALSE) {
				        //$result[] = $data;
				        //LGROUP|ECOMMERCE|Installation|MC-SAP1|MC-SAP2|MC-SAP3|SERIES|Article_CODE|NAME_TH|NAME_EN|DESC_TH|DESC_EN|MAIN_MATERIAL_TH|MAIN_MATERIAL_EN|MATERIAL_TH|MATERIAL_EN|COLOR_TH|COLOR_EN|SIZE|KEYFEATURE_TH|KEYFEATURE_EN|GOODKNOW_TH|GOODKNOW_EN|INSTRUCTION_TH|INSTRUCTION_EN|PRICE|JoyPrice|GermanMelamine|E1|SuperFlex|Crystallized|Warranty25|PianoHi|ItalianVeneer|DontMiss|CowLeather|HiQuanlity|TemperedGlass|SafetyGlassw
				        //skip header row
				        if($data[0] == 'LGROUP' || $data[1] == 'ECOMMERCE' || $data[2] == 'QTY'){
				        	continue;
				        }

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
							if(isset($orderqty[$item['product_id']])){
								$orderqty[$item['product_id']] += $item['qty_ordered'];
							} else {
								$orderqty[$item['product_id']] = $item['qty_ordered'];
							}
						}

				    }

				  	// prepare to load products
				    $product = Mage::getModel('catalog/product');
				    foreach ($csvdata as $sku => $item) {
				    	$qty = $item['qty'];

				    	// ignored
				    	$warehouse = $item['warehouse'];

						$p = $product->loadByAttribute('sku', $sku);

    					if ($p) {
      						
    						Mage::log('found sku : '.$sku.' || update qty : '.$qty, null, $filelogName);
      						// get product's stock data such quantity, in_stock etc
     						$productId = $p->getIdBySku($sku);

     						//calculate real stock
     						if(isset($orderqty[$productId])){
     							$qty = $qty - $orderqty[$productId];
     						}

      						$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
							$stockItemId = $stockItem->getId();
							$stock = array();							
							
							// then set product's stock data to update
							if (!$stockItemId) {
								$stockItem->setData('product_id', $product->getId());
								$stockItem->setData('stock_id', 1);
							} else {
								$stock = $stockItem->getData();
							}
							$stockItem->setData('qty', $qty);
							if ($qty > 0) {
								$stockItem->setData('is_in_stock', 1);
							} else {
								$stockItem->setData('is_in_stock', 0);
							}
							$stockItem->setData('manage_stock', 1);
							$stockItem->setData('use_config_manage_stock', 1);
							
							// call save() method to save your product with updated data
							try{
								$stockItem->save();
								$product->save($p);
							} catch (Exception $ex) {
								// handle the error here!!
								Mage::log('error sku : '.$sku, null, $filelogName);
							}
							unset($stockItem);
							unset($p);
						} else {
							Mage::log("SKU not found : ".$sku, null, $filelogName);
						}

				    }

					fclose($handle);
					Mage::log('close file : '.$dir.$filenamecsv, null, $filelogName);

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
						Mage::log('removed : '.$dir.$filename, null, $filelogName);
					}else{
						Mage::log('can not removed : '.$dir.$filename, null, $filelogName);
					}

					// check to move file
					if (!file_exists($dir.$filenamecsv)) {
						Mage::log('moved to completed : '.$newdir.$filenamecsv, null, $filelogName);
					}else{
						Mage::log('can not moved : '.$newdir.$filenamecsv, null, $filelogName);
					}
				}

		    }
		}

   	}
}
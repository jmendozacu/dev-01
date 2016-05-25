<?php
class MarginFrame_Sync_Model_Cron_Stock extends Mage_Core_Model_Abstract
{
	
	public function Run() {
		// /var/interface/stock
		$dir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'stock'.DS;

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
				    Mage::log('=========================================================', null, 'mgfsync_stock.log');
				    Mage::log('open file : '.$dir.$filenamecsv, null, 'mgfsync_stock.log');

				    $csvdata = array();
				    while (($data = fgetcsv($handle, 1000, "|")) !== FALSE) {
				        //$result[] = $data;
				        //skip header row
				        // if($data[0] == 'ARTICLE' || $data[1] == 'Warehouse' || $data[2] == 'QTY'){
				        // 	continue;
				        // }
				        if($row == 1){ $row++; continue; }

				        // array(
				        // 	'sku'=>array(
				        // 		'warehouse'=>'1100',
				        // 		'qty'=>'10'
				        // 	)
				        // )
				        $csvdata[$data[0]]['warehouse'] = $data[1];
				        $csvdata[$data[0]]['qty'] = $data[2];
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
      						
    						Mage::log('found sku : '.$sku.' || update qty : '.$qty, null, 'mgfsync_stock.log');
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
								Mage::log('error sku : '.$sku, null, 'mgfsync_stock.log');
							}
							unset($stockItem);
							unset($p);
						} else {
							Mage::log("SKU not found : ".$sku, null, 'mgfsync_stock.log');
						}

				    }

					fclose($handle);
					Mage::log('close file : '.$dir.$filenamecsv, null, 'mgfsync_stock.log');

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
						Mage::log('removed : '.$dir.$filename, null, 'mgfsync_stock.log');
					}else{
						Mage::log('can not removed : '.$dir.$filename, null, 'mgfsync_stock.log');
					}

					// check to move file
					if (!file_exists($dir.$filenamecsv)) {
						Mage::log('moved to completed : '.$newdir.$filenamecsv, null, 'mgfsync_stock.log');
					}else{
						Mage::log('can not moved : '.$newdir.$filenamecsv, null, 'mgfsync_stock.log');
					}
				}

		    }
		}

   	}
}
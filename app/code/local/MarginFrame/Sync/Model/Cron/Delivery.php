<?php
class MarginFrame_Sync_Model_Cron_Delivery extends Mage_Core_Model_Abstract
{

	public function Run() {

		$dir = Mage::getBaseDir('var').DS.'interface'.DS.'import'.DS.'delivery'.DS.'ready'.DS;

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

		    	// rename file .trg to .crv
		    	$filenamecsv = str_replace('.trg', '.csv', $filename);

				$row = 1;

				if (($handle = fopen($dir.$filenamecsv, "r")) !== FALSE) {
					Mage::log('=========================================================', null, 'mgfsync_delivery.log');
				    Mage::log('open file : '.$dir.$filenamecsv, null, 'mgfsync_delivery.log');
				    
				    $ordercsv = array();
				    // prepare to load products
				    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				        //skip header row
				        if(
				        	$data[0] == 'order_no'|| 
				        	// $data[1] == 'shipment_no'|| 
				        	$data[1] ==	'sku'||
				        	$data[2] ==	'ordered_qty'||
				        	$data[3] ==	'shipped_qty'||
				        	$data[4] ==	'delivery_method'||
				        	$data[5] ==	'tracking_no'
				        	)
				        {
				        	continue;
				        }

						$order_no 			= $data[0];
						// $shipment_no 		= $data[1];
						$sku 				= $data[1];
						$ordered_qty 		= $data[2];
						$shipped_qty 		= $data[3];
						$delivery_method 	= $data[4];
						$tracking_no 		= $data[5];

						$value = array();
						if(isset($ordercsv[$order_no])){
							$value = $ordercsv[$order_no];
						}

						$value[] = array(
							// 'shipment_no' => $shipment_no,
							'sku' => $sku,
							'ordered_qty' => $ordered_qty,
							'shipped_qty' => $shipped_qty,
							'delivery_method' => $delivery_method,
							'tracking_no' => $tracking_no,

						);

						$ordercsv[$order_no] = $value;

						// save data
						// shipped_qty = QTY that can be shipped
						// delivery_method  = for example EMS or Alpha. We will only show information on frontend
						// tracking_no = tracking no related to delivery method
						// update by order_no & sku!!!

				    }

					fclose($handle);
					Mage::log('close file: '.$dir.$filenamecsv, null, 'mgfsync_delivery.log');

					foreach ($ordercsv as $order_no => $inputs) {

						try {
							Mage::log('-- ORDER: '.$order_no, null, 'mgfsync_delivery.log');

							$order = Mage::getModel('sales/order')->loadByIncrementId($order_no);
							$items = $order->getAllVisibleItems();
							//$items = $order->getAllItems();

							$itemqty = array();
							// $shipmentno = '';

							foreach ($items as $item) {

								foreach ($inputs as $input) {

									if($item->getSku() == $input['sku']) {

										$itemqty[$item->getId()] = $input['shipped_qty'];
										// $shipmentno = $input['shipment_no'];
										break;
									}

								}
							}
// var_dump($order->canShip()); die;
							if($order->canShip()) {

						        Mage::log('-- order->canShip() ', null, 'mgfsync_delivery.log');

						        $carrier_type = 'custom';
						        $carrier_name = $input['delivery_method'];
						        $trackNumber = $input['tracking_no'];

						        //carrier_code by shipyours
						   //      $carrier_codes = array(
							  // 		1 => 'EMS',
									// 2 => 'ลงทะเบียน',
									// 3 => 'LAZADA',
									// 4 => 'KERRY',
									// 5 => 'MESSENGER',
									// 6 => 'อื่นๆ',
									// 7 => 'UPS',
									// 8 => 'FedEx',
									// 9 => 'ลงทะเบียนต่างประเทศ',
									// 10 => 'EMS ต่างประเทศ',
									// 11 => 'รับเอง',
									// 12 => 'alpha',
									// 13 => 'DHL'	
						   //      );

						        // if(isset($carrier_codes[$carrier_name])){
						        // 	$carrier_name = $carrier_codes[$carrier_name];
						        // }
						        // else {
						        // 	$carrier_name = 'อื่นๆ';
						        // }

						        if(empty($trackNumber)){
						        	$trackNumber = '0000000000';
						        }

						        //Create shipment
						        try {

							        $shipmentIncrementId = Mage::getModel('sales/order_shipment_api')->create($order->getIncrementId(), $itemqty);

							        if($shipmentIncrementId) {
							        	
							        	Mage::log('-- shipmentIncrementId: '.$shipmentIncrementId, null, 'mgfsync_delivery.log');

							        	//Add tracking information
								        $tracking = Mage::getModel('sales/order_shipment_api')->addTrack($shipmentIncrementId, $carrier_type, $carrier_name, $trackNumber);

									    //send email
									    $ship_data = $order->getData();
									    $customerEmail = $ship_data['customer_email'];				    
									    $customerEmailComments = '';
									    $sent = 0;

									    Mage::log(' customerEmail: '.$customerEmail, null, 'mgfsync_delivery.log');

									    if (!is_null($customerEmail)) {
									        $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);
									        $sent = $shipment
									        	->sendEmail(true, $customerEmailComments)
									        	->setEmailSent(true)->save();
									        Mage::log(' setEmailSent ', null, 'mgfsync_delivery.log');
									    }

										//update order status
								        // $comment = 'Automatically shipment (ref no: ' . $shipmentno . ')';
								        // $order = Mage::getModel('sales/order')->loadByIncrementId($order_no);
								        // $order->addStatusHistoryComment($comment);

										$order->save();
										Mage::log('-- order->save() ', null, 'mgfsync_delivery.log');
							        }
						        }
						        catch(Exception $ex){
						        	Mage::log('-- ERROR : '.$order_no.' : '.$ex->getMessage(), null, 'mgfsync_delivery.log');
						        }

		
    						}
    					} catch(Exception $ex2){
    						Mage::log('-- ERROR: '.$ex2->getMessage(), null, 'mgfsync_delivery.log');
    					}
					}

					// moved file to completed path
					$newdir = str_replace(DS.'ready'.DS, DS.'completed'.DS, $dir);

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
						Mage::log('removed : '.$dir.$filename, null, 'mgfsync_delivery.log');
					}else{
						Mage::log('can not removed : '.$dir.$filename, null, 'mgfsync_delivery.log');
					}

					// check to move file
					if (!file_exists($dir.$filenamecsv)) {
						Mage::log('moved to completed : '.$newdir.$filenamecsv, null, 'mgfsync_delivery.log');
					}else{
						Mage::log('can not moved : '.$newdir.$filenamecsv, null, 'mgfsync_delivery.log');
					}
					
				} //end if open csv

		    } //end if find .trg file

		} //end while

	}
	
}
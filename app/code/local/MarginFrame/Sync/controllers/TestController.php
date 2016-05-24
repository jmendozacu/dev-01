<?php

class MarginFrame_Sync_TestController extends Mage_Core_Controller_Front_Action
{
	
	function dmailAction(){
		echo Mage::helper('sales')->canSendNewShipmentEmail(1);
		echo Mage::helper('sales')->canSendNewShipmentEmail(2);

		$shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId('100000031');
	    $sent = $shipment->sendEmail(true, '')
	    ->setEmailSent(true)
	    ->save();
	}

	function testAction(){
		$model = Mage::getModel('mgfsync/cron_stock');
		echo $model::Run();
	}

	function tcancelAction(){
		$c = new Appmerce_AutoCancel_Model_Cron();
		$c->autoCancel();
	}

	function stockrunAction(){
		Mage::getModel('mgfsync/cron_stock')->Run();
	}

	function shippingrunAction(){
		//var_dump('shippingrunAction');
		//$shipping = Mage::getModel('mgfsync/cron_shipping');
		//$shipping->Run();
	}

	function billingrunAction(){
		Mage::getModel('mgfsync/cron_billing')->Run();
	}

	function deliveryrunAction(){
		Mage::getModel('mgfsync/cron_delivery')->Run();
	}

	function billcsvAction()
	{			
		$collection = Mage::getResourceModel('sales/order_collection');
		//table :: sales_flat_order
		//======================================
		$collection->getSelect()
		->reset(Zend_Db_Select::COLUMNS)
	    ->columns(
	    	array(
    		'order_no' => 'increment_id',
    		'subtotal' => 'subtotal_incl_tax',
    		'discount_amount' => 'discount_amount',
    		'shipping_amount' => 'shipping_amount',
    		'tax_amount' => 'tax_amount',
    		'grand_total' => 'grand_total',
    		'shipping_method' => 'shipping_method',
    		'created_at' => 'created_at',
    		'store_id' => 'store_id',
    		)
    	);

	    //table :: sale_order_tax
		//======================================
		/*
		$collection
		->getSelect()
		->joinLeft(array('order_tax'=> 'sales_order_tax'),
			'order_tax.order_id = main_table.entity_id',
			array( 
				'tax_id' => 'order_tax.tax_id'
			)
		);
		*/

	    //table :: sales_flat_order_status_history
		//======================================
		/*
		$collection
		->getSelect()
		->joinLeft(array('order_status_history'=> 'sales_flat_order_status_history'),
			'order_status_history.parent_id = main_table.entity_id',
			array( 
				'remark' => 'order_status_history.comment'
			)
		);
		*/
		
		//table :: sales_flat_invoice
		//======================================
		$collection
		->getSelect()
		->join(array('order_invoice'=> 'sales_flat_invoice'),
			'order_invoice.order_id = main_table.entity_id',
			array( 
				'invoice_date' => 'order_invoice.created_at',
				'invoice_no' => 'order_invoice.increment_id'
			)
		);
		
		//table :: sales_flat_order_payment
		//======================================
		$collection
		->getSelect()
		->joinLeft(array('order_payment'=> 'sales_flat_order_payment'),
			'order_payment.parent_id = main_table.entity_id',
			array( 
				'payment_method' => 'order_payment.method'
			)
		);
		//table :: sales_flat_order_item
		//======================================
		$collection
		->getSelect()
		->joinLeft(array('order_item'=> 'sales_flat_order_item'),
			'order_item.order_id = main_table.entity_id',
			array( 
				'sku' => 'order_item.sku',
				'product_name' => 'order_item.name',
				'item_row_total' => 'order_item.row_total_incl_tax',
				'item_price' => 'order_item.price_incl_tax',
				'qty' => 'order_item.qty_ordered',
				'product_options' => 'order_item.product_options'
			)
		)
		->where('order_item.parent_item_id is null');
		
		//table :: sales_flat_order_address
		//======================================
		$collection
		->getSelect()
		->joinLeft(array('billing_address'=> 'sales_flat_order_address'),
			'billing_address.entity_id = main_table.billing_address_id',
			array( 
				
				'billing_postcode' => 'billing_address.postcode',
				'billing_prefix' => 'billing_address.prefix',
				'billing_firstname' => 'billing_address.firstname',
				'billing_lastname' => 'billing_address.lastname',
				'billing_company' => 'billing_address.company',
				'billing_telephone' => 'billing_address.telephone',
				'billing_ext' => '', //'billing_address.telephone_ext',
				//'billing_building' => 'billing_address.building',
				'billing_street' => 'billing_address.street',
				'billing_subdistrict' => 'billing_address.subdistrict_id',
				'billing_district' => 'billing_address.city_id',
				'billing_province' => 'billing_address.region_id',
				'billing_country' => 'billing_address.country_id',
			)
		);

		$collection
		->getSelect()
		->joinLeft(array('shipping_address'=> 'sales_flat_order_address'),
			'shipping_address.entity_id = main_table.shipping_address_id',
			array( 
				
				'shipping_postcode' => 'shipping_address.postcode',
				'shipping_prefix' => 'shipping_address.prefix',
				'shipping_firstname' => 'shipping_address.firstname',
				'shipping_lastname' => 'shipping_address.lastname',
				'shipping_company' => 'shipping_address.company',
				'shipping_telephone' => 'shipping_address.telephone',
				'shipping_ext' => '', //shipping_address.telephone_ext',
				//'shipping_building' => 'shipping_address.building',
				'shipping_street' => 'shipping_address.street',
				'shipping_subdistrict' => 'shipping_address.subdistrict_id',
				'shipping_district' => 'shipping_address.city_id',
				'shipping_province' => 'shipping_address.region_id',
				'shipping_country' => 'shipping_address.country_id',
			)
		);


		$collection->getSelect()
		//->where('exported_name is null')
		->distinct()
		->order('created_at desc')
		;

		//echo (string)$collection->getSelect(); die;

		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		//summary
		$q = $read->fetchAll((string)$collection->getSelect()); 

		if(!empty($q)) {
			$csvcol = array(
				'payment_method',
				'shipping_country',
				'shipping_method',
				'shipping_postcode',				
				'order_no',
				'sku',		
				'qty',
				'shipping_prefix',
				'shipping_firstname',
				'shipping_lastname',
				'shipping_company',
				'shipping_telephone',
				'shipping_ext',
				'shipping_mobile',
				'shipping_street',
				'shipping_subdistrict',
				'shipping_district',
				'shipping_province',
				'remark',
				'created_at',
				'product_name'
			);
			$csvcolbill = array(
				'payment_method',
				'billing_country',
				'shipping_method',
				'billing_postcode',
				'order_no',
				'invoice_no',
				'sku',
				'qty',
				'item_price',
				'item_row_total',
				'subtotal',
				'discount_amount',
				'shipping_amount',
				'tax_amount',
				'grand_total',
				'tax_id',
				'billing_prefix',
				'billing_firstname',
				'billing_lastname',
				'billing_company',
				'billing_telephone',
				'billing_ext',
				'billing_mobile',
				'billing_street',
				'billing_subdistrict',
				'billing_district',
				'billing_province',
				'shipping_prefix',
				'shipping_firstname',
				'shipping_lastname',
				'shipping_company',
				'shipping_telephone',
				'shipping_ext',
				'shipping_mobile',
				'shipping_street',
				'shipping_subdistrict',
				'shipping_district',
				'shipping_province',
				'remark',
				'created_at',
				'product_name',
				'invoice_date',
				'product_size',
				'language'
			);

			$csvresult = array();
			$csvresultbill = array();
			$orderids = array();
			$csvresult[] = $csvcol;
			$csvresultbill[] = $csvcolbill;
			foreach ($q as $row) {

				$orderids[] = $row['order_no'];
				$row['created_at'] = Mage::getModel('core/date')->date('y/m/d', strtotime($row['created_at']));
				$row['invoice_date'] = Mage::getModel('core/date')->date('y/m/d', strtotime($row['invoice_date']));

				//var_dump('store_id = '. $row['store_id']); die;
				$row['language'] = Mage::getStoreConfig('general/locale/code', $row['store_id']);

				$opts = $row['product_options'];
				$opts = unserialize($opts);
				$infos = array();
				foreach ($opts['attributes_info'] as $info) {
					if($info['label'] == 'Size') {
						$row['product_size'] = $info['value'];
					}
					else {
						$infos[] = $info['label'].':'.$info['value'];
					}
				}
				if(count($infos)) {
					$row['product_name'] .= ' ('.implode(', ', $infos).')';
				}
				//print_r($opts['attributes_info']); die;

				//var_dump($row['payment_method']); var_dump($row['shipping_method']);
				if(isset($row['payment_method']) && $row['payment_method'] != '') {
					$row['payment_method']  = Mage::getStoreConfig('payment/'.$row['payment_method'].'/title');
					if(preg_match("/\>(?P<title>[^<]+)/i", $row['payment_method'], $match)){
						$row['payment_method'] = trim($match['title']);
					}
				}

				//if(isset($row['shipping_method']) && $row['shipping_method'] != '') {
				//	$row['shipping_method'] = Mage::getStoreConfig('carriers/'.explode('_', $row['shipping_method'])[0].'/title');
				//}
				//var_dump($row['payment_method']); var_dump($row['shipping_method']);

				//shipping
				$csvrow = array();			
				foreach ($csvcol as $col) {
					if(isset($row[$col])) {
						$csvrow[] = $row[$col];
					}
					else {
						$csvrow[] = '';
					}
				}
				$csvresult[] = $csvrow;

				//billing
				$csvrowbill = array();
				foreach ($csvcolbill as $col) {
					if(isset($row[$col])) {
						$csvrowbill[] = $row[$col];
					}
					else {
						$csvrowbill[] = '';
					}
				}
				$csvresultbill[] = $csvrowbill;
			}

			var_dump($csvresultbill);
			var_dump($csvresult);
		
		}
	}


}
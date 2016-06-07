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
		$model = Mage::getModel('mgfsync/cron_productmaster');
		echo $model::Run();
		// $profile = Mage::getModel('ecommerceteam_dataflow/profile_import')->getCollection();
		// echo "<pre>";
		// // print_r(Mage::helper('mgfsync/data')->reindex());
		// print_r($profile->getData());
		// echo "</pre>";
	}

	function tcancelAction(){
		$c = new Appmerce_AutoCancel_Model_Cron();
		$c->autoCancel();
	}

	function stockrunAction(){
		Mage::getModel('mgfsync/cron_stock')->Run();
	}

	function statusrunAction(){
		Mage::getModel('mgfsync/cron_productstatus')->Run();
	}

	function storerunAction(){
		Mage::getModel('mgfsync/cron_store')->Run();
	}

	function pricerunAction(){
		Mage::getModel('mgfsync/cron_price')->Run();
	}

	function productrunAction(){
		Mage::getModel('mgfsync/cron_price')->Run();
	}

}
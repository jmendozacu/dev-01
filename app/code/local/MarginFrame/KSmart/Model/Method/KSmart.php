<?php
class MarginFrame_KSmart_Model_Method_KSmart extends Mage_Payment_Model_Method_Abstract
{

	protected $_formBlockType = 'KSmart/form_KSmart';

    protected $_infoBlockType = 'KSmart/info_KSmart';

 //    protected $_canSaveKSmart    = false;

	protected $_code  = 'KSmart';


	/**
		Override the method that action when a customer clicked checkout
	**/
	public function getOrderPlaceRedirectUrl(){
		return Mage::getUrl('KSmart/KSmart/redirect');
	}


	public function isAvailable($quote = null){
		$isActive =  (bool)(int)Mage::getStoreConfig('payment/KSmart/active');

		return $isActive;
		$CurrentAmount = (double)Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal();

		//=> Allowed IP Address
		if (trim(Mage::getStoreConfig('payment/KSmart/allowedip')) != "") {
			$ClientIP = long2ip(Mage::helper('core/http')->getRemoteAddr(true)); 
			if (trim(Mage::getStoreConfig('payment/KSmart/allowedip')) != $ClientIP) {
				$isActive = false;
			}
		}

		//=> Filter Limited
		$LoopNo = 1;
		$cartcondition = true;
		$AvaliableArray = array();
		$storeId = Mage::app()->getStore()->getStoreId();
		$session= Mage::getSingleton('checkout/session');
		foreach($session->getQuote()->getAllItems() as $item)
		{	
			$productsku = $item->getSku();
			$productname = $item->getName();
			$productqty = $item->getQty();
			$ItemRowPrice = $item->getRowTotal();
			if (($ItemRowPrice > 0) && ($cartcondition == true)) {
				//$ItemCount++;
				$productid = $item->getProductId();
				//=> Get Attibute data
				$ProductInstallmentArray = array();
				$attributeValue = null;		
				$attributeValue = "";
				$product = Mage::getModel('catalog/product')->load($productid);
				
				$PrdinstallmentsData = $product->getData('installments_attribute');
				if ($PrdinstallmentsData != "")  {
					$PrdInstallmentArray = explode(",", $PrdinstallmentsData);
					foreach ($PrdInstallmentArray as $PrdInstallmentItem) {
						//=> KBank A
						if (Mage::getStoreConfigFlag('payment/KSmart/active', $storeId)) {
							if (Mage::getStoreConfig('payment/KSmart/filterlimit', $storeId)==$PrdInstallmentItem) {
								$PlanActive = true;
								if (trim(Mage::getStoreConfig('payment/KSmart/min_order_total')) != "") {
									$PlanActive = $PlanActive && ($CurrentAmount >= (double)Mage::getStoreConfig('payment/KSmart/min_order_total'));
								}
					
								if (trim(Mage::getStoreConfig('payment/KSmartpay/max_order_total')) != "") {
									$PlanActive = $PlanActive && ($CurrentAmount <= (double)Mage::getStoreConfig('payment/KSmart/max_order_total'));
								}
								
								if ($PlanActive) {
    								$xtext = Mage::getStoreConfig('payment/KSmart/bjcmethod', $storeId);
									$ProductInstallmentArray[$PrdInstallmentItem] = $xtext;
								}
							}
						}
					}// End Foreach
				}
			}
		}

	}
}
?>
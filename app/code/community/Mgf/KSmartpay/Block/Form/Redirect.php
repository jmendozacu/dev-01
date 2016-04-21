<?php 
 class Mgf_KSmartpay_Block_Form_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {

		$KSmartpay = Mage::getModel('KSmartpay/method_KSmartpay');
		$xData = "";
		$MD5Code = Mage::getStoreConfig('payment/KSmartpay/md5code');
		
		$PaymentGatewayName = "";

		$payMerchantID = "";
		$payTerminalID = "";		
		$payOrderNo = "";
		$PayAmount = "";
		$PayAmountNumber = 0;
		$PayDescription = "";		
		
		$payCustomerID = 0;
		$payCustomerName = "";
		$payCustomerEmail = "";
		$payCustomerTel = "";
		
		$paybycardcode = "";
		$paybycard = "";
		$paybycardterm = "";
		$payInstallRate = 0;
		
		$PayChannel = "";
		$PayExpiredDate = "";

		$reference = "";
        foreach ($KSmartpay->getStandardCheckoutFormFields('redirect') as $field=>$value) {
			//echo "<li>" . $field . " :: " . $value . "</li>";
			switch (strtolower(trim($field))) {
			    case "merchant2":
			        $payMerchantID = trim($value);
			        break;
			    case "term2":
			        $payTerminalID = trim($value);
			        break;
			    case "invmerchant":
			        $payOrderNo = trim($value);
			        $transaction = Mage::getModel('KSmartpay/transaction');
			        $reference = $transaction->reGenerateOrderId($payOrderNo);
			        break;
			    case "amount2":
					$PayAmount = trim(substr($value, 0, 10) . "." . substr($value, 10));
					$PayAmountNumber = $PayAmount;
			        break;
			    case "detail2":
			        $PayDescription = trim($value);
			        break;
			    case "customer_name":
			        $payCustomerName = trim($value);
			        break;
			    case "customer_email":
			        $payCustomerEmail = trim($value);
			        break;
			    case "customer_phone":
			        $payCustomerTel = trim($value);
			        break;
			    case "customer_phone":
			        $payCustomerTel = trim($value);
			        break;
			    case "installment_card":
			        $paybycard = trim($value);
			        break;
			    case "payterm2":
			        $paybycardterm = trim($value);
			        break;
			}
        }
		
		//echo "xxxxxxxxxxxxxxxxxxxxxxxxxxxx " . $paybycard . "<br/>";
		
		//exit;
		$PaymentMethod = "";
		$PaymentGatewayUrl = "";
		if (preg_match('#^KSmartpay#', $paybycard) === 1) {
			//=> KSmartpayA,KSmartpayB,KSmartpayC,KSmartpayD,KSmartpayE
			//echo  "tttttttttttttttt<br/>";
			$PaymentMethod = "KSmartPay";
			$PaymentGatewayName = "K-Payment Gateway";
			$PaymentGatewayUrl = trim(Mage::getStoreConfig("payment/KSmartpay/paymentgatewayurl"));
		}
		if (preg_match('#^Krungsri#', $paybycard) === 1) {
			//=> KrungsriA,KrungsriB,KigrungsriX,KrungsriY,KrungsriZ,KrungsriC,KrungsriD,KrungsriP,KrungsriQ,KrungsriR,KrungsriE,KrungsriF,KrungsriS,KrungsriT,KrungsriU,KrungsriG,KrungsriH,KrungsriL,KrungsriM,KrungsriN,KrungsriI,KrungsriJ,KrungsriO,KrungsriV,KrungsriW
			//echo "kwwwwwwwwwwwwwwwwwwwwwwwwwww<br/>";
			$PaymentMethod = "Krungsri";
			$PaymentGatewayName = "Krungsri Payment Gateway";
			$PaymentGatewayUrl = trim(Mage::getStoreConfig("payment/KSmartpay/krungsrigatewayurl"));
			if ($PaymentGatewayUrl == "") {
				//$PaymentGatewayUrl = "https://servicekrungsrigroup.com/epp/payment";
			}
			
			$payMerchantID = trim(Mage::getStoreConfig("payment/KSmartpay/krungsrimerchantno"));
			if ($payMerchantID =="") {
				//$payMerchantID = "270019";
			}
			$paybycardcode = trim(Mage::getStoreConfig("payment/". trim($paybycard)  ."/shopid"));
			if ($paybycardcode=="") {
				$paybycardcode = "creditcard";
			}
			
			$payInstallRate = trim(Mage::getStoreConfig("payment/". trim($paybycard)  ."/paymenttermrate"));
			$payInstallRate = $payInstallRate / 100;
			
		}
		if (preg_match('#^Hsbc#', $paybycard) === 1) {
			//=> HsbcA,HsbcB,HsbcC,HsbcD,HsbcE
			$PaymentMethod = "Hsbc";
		}
		if (preg_match('#^Scb#', $paybycard) === 1) {
			//=> ScbA,ScbB,ScbC,ScbD,ScbE
			$PaymentMethod = "Scb";
		}
		if (preg_match('#^Bbl#', $paybycard) === 1) {
			//=> BblA,BblB,BblC,BblD,BblE
			$PaymentMethod = "Bbl";
		}
		
		//echo $PaymentMethod . "<br/>" . $PaymentGatewayUrl . "<br/>" . $payMerchantID . "<br/>" . $paybycardcode . "<br/>" . $payInstallRate . "<br/>" . $PaymentGatewayName . "<br/>";
		
		//exit;
		
        $form = new Varien_Data_Form();
        $form->setAction($PaymentGatewayUrl)
            ->setId('KSmartpay_standard_checkout')
            ->setName('sendform')
            ->setMethod('post')
		    ->setUseContainer(true);
					
		//=>
		switch ($PaymentMethod) {
		    case "KSmartPay":
				//=> Build post form		
		        foreach ($KSmartpay->getStandardCheckoutFormFields('redirect') as $field=>$value) {
				    if ((strtolower(trim($field)) == "customer_name") || (strtolower(trim($field)) == "installment_card")  || (strtolower(trim($field)) == "customer_email") || (strtolower(trim($field)) == "customer_phone")) {
					
					}
					else {
					   //=> Form
					   if (strtolower(trim($field)) != "txtchecksum") {
					   		 if ((strtolower(trim($field)) == "shopid") || (strtolower(trim($field)) == "payterm2") || (strtolower(trim($field)) == "invmerchant")) {
							 	if ((strtolower(trim($field)) == "shopid") && (trim($value) != "")) {
						   			$form->addField($field, 'hidden', array('name'=>$field, 'value'=>trim($value)));
						   			$xData .= trim($value);
								}
							 	if ((strtolower(trim($field)) == "payterm2") && (trim($value) != "")) {
						   			$form->addField($field, 'hidden', array('name'=>$field, 'value'=>trim($value)));
						   			$xData .= trim($value);
								}
								if ((strtolower(trim($field)) == "invmerchant") && (trim($reference) != "")) {
						   			$form->addField($field, 'hidden', array('name'=>$field, 'value'=>trim($reference)));
						   			$xData .= trim($reference);
								}
								
							 }
							 else {
					   			$form->addField($field, 'hidden', array('name'=>$field, 'value'=>trim($value)));
					   			$xData .= trim($value);
							}
					   }
					   else {
					   		$HashValue = md5($xData . $MD5Code);
					   		$form->addField($field, 'hidden', array('name'=>"CHECKSUM", 'value'=>$HashValue));
					   }
				   	  //=> End Form
				   }		   
		        }
				//=> Build post form end	
				//echo "vvvvvvvvvvvvv<br/>";
		        break;
		    case "Krungsri":
				//=> Build post form	
				
				//echo "ddddddddddddd<br/>";
				$form->addField("mid", "hidden", array("name"=>"mid", "value"=>trim($payMerchantID)));
				$form->addField("order_no", "hidden", array("name"=>"order_no", "value"=>trim($payOrderNo)));
				$form->addField("card_type", "hidden", array("name"=>"card_type", "value"=>trim($paybycardcode)));
				$form->addField("term1", "hidden", array("name"=>"term1", "value"=>trim($paybycardterm)));
				$form->addField("interest1", "hidden", array("name"=>"interest1", "value"=>trim($payInstallRate)));
				$form->addField("term2", "hidden", array("name"=>"term2", "value"=>trim("")));
				$form->addField("interest2", "hidden", array("name"=>"interest2", "value"=>trim("")));
				$form->addField("total_price", "hidden", array("name"=>"total_price", "value"=>$PayAmountNumber));
				$form->addField("description", "hidden", array("name"=>"description", "value"=>trim($PayDescription)));
				//echo "eeeeeeeeeeee<br/>";
				//=> Build post form end
		        break;
		}		
		
		$payOrderID = 0;
		if ($order = Mage::getModel('sales/order')) {
			$order->loadByIncrementId($payOrderNo);		
			$payOrderID = $order->getEntityId();
		}		
		
		$order = Mage::getModel('sales/order');
		$order->loadByIncrementId($payOrderNo);
		$payment = $order->getPayment();
		
		$payment->setAdditionalInformation('reference',$reference);
		
		$payment->setAdditionalInformation('bank',$PaymentMethod);
		$payment->setAdditionalInformation('card',$paybycard);
		$payment->setAdditionalInformation('installmentterms',$paybycardterm);
		
		$payment->setCcOwner($PaymentMethod);
		$payment->setCcType($paybycard);
		$payment->setPoNumber($paybycardterm);		
		$payment->setCcExpYear(trim(Mage::getStoreConfig("payment/". trim($paybycard)  ."/title")));

		
		$payment->save();

        $html = '<html>
				<body style="text-align:center;margin: auto;position: absolute; top: 0; left: 0; bottom: 0; right: 0;">';
		if ((trim(Mage::getStoreConfig('payment/KSmartpay/redirecttext')) == "") && (trim(Mage::getStoreConfig('payment/KSmartpay/redirectfooter')) == "")) {		
	      	 $html.= $this->__('<p>You will be redirected to ". $PaymentGatewayName ." in a few seconds.</p>');
		   	 $html.= $this->__('<p>Copyright ". $PaymentGatewayName ."</p>');
		 }
		 else {
		 	 if (trim(Mage::getStoreConfig('payment/KSmartpay/redirecttext')) != "") { 
	      	 	$html.= $this->__("<p>". Mage::getStoreConfig('payment/KSmartpay/redirecttext') ."</p>");
			 }
			 if (trim(Mage::getStoreConfig('payment/KSmartpay/redirectfooter')) != "") {	
		   	 	$html.= $this->__("<p>". Mage::getStoreConfig('payment/KSmartpay/redirectfooter') ."</p>");
			 }
		 
		 }
		
		$RedirectTime = "1000";
		if (trim(Mage::getStoreConfig('payment/KSmartpay/redirecttime')) != "") {
			$RedirectTime = Mage::getStoreConfig('payment/KSmartpay/redirecttime');
		}

       $html.= $form->toHtml();
       $html.= '<script type="text/javascript">
	   			  function formsubmit()
				  {
				  	document.sendform.submit();	
				  }
				  setTimeout("formsubmit()", '. $RedirectTime .');
	            </script>';
	  
        $html.= '</body></html>';
        return $html;
	
    }
}
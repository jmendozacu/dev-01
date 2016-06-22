<?php 
 class Mgf_KBank_Block_Form_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {

		$KBank = Mage::getModel('KBank/method_KBank');
		$xData = "";
		$MD5Code = Mage::getStoreConfig('payment/KBank/md5code');
		
		// $PaymentGatewayName = "";

		// $payMerchantID = "";
		// $payTerminalID = "";		
		// $payOrderNo = "";
		// $PayAmount = "";
		// $PayAmountNumber = 0;
		// $PayDescription = "";		
		
		// $payCustomerID = 0;
		// $payCustomerName = "";
		// $payCustomerEmail = "";
		// $payCustomerTel = "";
		
		// $paybycardcode = "";
		// $paybycard = "";
		// $paybycardterm = "";
		// $payInstallRate = 0;
		
		// $PayChannel = "";
		// $PayExpiredDate = "";

		// $reference = "";
		$form = $KBank->getStandardCheckoutFormFields('redirect');
		
		$form = new Varien_Data_Form();
        $form->setAction(Mage::getStoreConfig('payment/KBank/paymentgatewayurl'))
            ->setId('KBank_standard_checkout')
            ->setName('sendform')
            ->setMethod('post')
		    ->setUseContainer(true);
		foreach ($KBank->getStandardCheckoutFormFields('redirect') as $field=>$value) {
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
						if ((strtolower(trim($field)) == "invmerchant") ) {
				   			$form->addField($field, 'hidden', array('name'=>$field, 'value'=>trim($value)));
				   			$xData .= trim($value);
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
		
        $html = '<html>
				<body style="text-align:center;margin: auto;position: absolute; top: 0; left: 0; bottom: 0; right: 0;">';
		if ((trim(Mage::getStoreConfig('payment/KBank/redirecttext')) == "") && (trim(Mage::getStoreConfig('payment/KBank/redirectfooter')) == "")) {		
	      	 $html.= $this->__('<p>You will be redirected to KBank in a few seconds.</p>');
		   	 $html.= $this->__('<p>Copyright KBank</p>');
		 }
		 else {
		 	 if (trim(Mage::getStoreConfig('payment/KBank/redirecttext')) != "") { 
	      	 	$html.= $this->__("<p>". Mage::getStoreConfig('payment/KBank/redirecttext') ."</p>");
			 }
			 if (trim(Mage::getStoreConfig('payment/KBank/redirectfooter')) != "") {	
		   	 	$html.= $this->__("<p>". Mage::getStoreConfig('payment/KBank/redirectfooter') ."</p>");
			 }
		 
		 }
		
		$RedirectTime = "1000";
		if (trim(Mage::getStoreConfig('payment/KBank/redirecttime')) != "") {
			$RedirectTime = Mage::getStoreConfig('payment/KBank/redirecttime');
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
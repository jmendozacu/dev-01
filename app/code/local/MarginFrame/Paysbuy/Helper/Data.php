<?php

class MarginFrame_Paysbuy_Helper_Data extends Mage_Payment_Helper_Data {
	
	public function debug($data , $exit = false){
		echo "<pre>";
		print_r($data);
		echo "</pre>";	
		if($exit){
			die("End Debug");
		}
	}

	public function checkTransection($invoice = null){

		$model = Mage::getModel('Paysbuy/method_paysbuy');
		$config = Mage::getStoreConfig('payment/Paysbuy');
		$soapUrl = $model->getPaysbuyTransectionUrl();
		$xml = array();
		$xml['psbID'] = $config['psbID'];
		$xml['biz'] = $config['username'];
		$xml['secureCode'] = $config['secureCode'];
		$xml['invoice'] = $invoice;
		$xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
		<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
		  <soap:Body>
		    <getTransactionByInvoice xmlns="http://tempuri.org/">';
			    foreach ($xml as $field=>$value) {
			    	$xml_post_string.='<'.$field.'>'.$value.'</'.$field.'>';
			    }
		$xml_post_string .='</getTransactionByInvoice>
		  </soap:Body>
		</soap:Envelope>';
		$headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: http://tempuri.org/getTransactionByInvoice", 
            "Content-length: ".strlen($xml_post_string),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $soapUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch); 
        curl_close($ch);

        // converting
        $soap     = simplexml_load_string($response);
        $response = $soap->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children()->getTransactionByInvoiceResponse->getTransactionByInvoiceResult->getTransactionByInvoiceReturn;
        return json_decode(json_encode($response),true);
	}
}

?>
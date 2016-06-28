<?php
class MarginFrame_Paysbuy_PaysbuyController extends Mage_Core_Controller_Front_Action
{
    
    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Get order
     *
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->_order == null) {
        }
        return $this->_order;
    }

    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton with Paysbuy strandard order transaction information
     *
     * @return Autoeye_Paysbuy_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('Paysbuy/standard');
    }

    public function testAction(){
    	$invoice = Mage::app()->getRequest()->getParam('invoice');
    	$response = Mage::helper('Paysbuy')->checkTransection($invoice);
    	echo '<pre>';
    	print_r($response);
    	echo '</pre>';
    }
    /**
     * When a customer chooses Paysbuy on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
		
		$session = Mage::getSingleton('checkout/session');
		$session->setPaysbuyStandardQuoteId($session->getQuoteId());
		$order = Mage::getModel('sales/order');
		$order->load(Mage::getSingleton('checkout/session')->getLastOrderId());

		$Paysbuy = Mage::getModel('Paysbuy/method_paysbuy');
    	$soapUrl = $Paysbuy->getPaysbuyUrl();
    	$xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
		<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
		  <soap:Body>
		    <api_paynow_authentication_v3 xmlns="http://tempuri.org/">';
	    foreach ($Paysbuy->getStandardCheckoutFormFields('redirect') as $field=>$value) {
	    	$xml_post_string.='<'.$field.'>'.$value.'</'.$field.'>';
	    }
		$xml_post_string .='</api_paynow_authentication_v3>
		  </soap:Body>
		</soap:Envelope>';
		$headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: http://tempuri.org/api_paynow_authentication_v3", 
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

        // converting
        $response = curl_exec($ch); 
        curl_close($ch);
        
        // converting
        $response = str_replace("<soap:Body>","",$response);
        $response = str_replace("</soap:Body>","",$response);
        $response = (string)simplexml_load_string($response)->api_paynow_authentication_v3Response->api_paynow_authentication_v3Result;

		if(substr($response,0,2) == "00"){
			// $order->sendNewOrderEmail();
			$order->save();
			$session = Mage::getSingleton('checkout/session');
			$refid = substr($response,2,strlen($response));
			$session->setRefIdPaysbuy($refid);
			$order->sendNewOrderEmail();
			$this->getResponse()->setBody($this->getLayout()->createBlock('Paysbuy/form_redirect')->toHtml());
		} else {
			
			$errMsg = substr($response,2,strlen($response));
			Mage::getSingleton('core/session')->addError($errMsg)->setData( 'validationMessages', $errMsg);
           	$this->_redirectUrl('/checkout/cart');
           	return;
		}

		// $session->unsQuoteId();

    }

    /**
     * When a customer cancel payment from Paysbuy.
     */
    public function cancelAction($PaysbuyCode,$PaysbuyState)
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getPaysbuyStandardQuoteId(true));

        // cancel order
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                //$order->cancel()->save();
				 if($order->canCancel()) {
				 	$order->cancel();
				 	$message="Cancel :: Paysbuy Return Code : " .  $PaysbuyCode . ", State : ". $PaysbuyState;
					$order->addStatusToHistory($order->getStatus(), $message, false);
					$order->save();
				 }
 
              
              $orderModel->setStatus('canceled_pendings');
              $orderModel->save();
			  
			  
				$state = Mage_Sales_Model_Order::STATE_CANCELED;
				
				$order->setState($state, true, $message);
				
            }
        }

		Mage::getSingleton('checkout/session')->addError("Paysbuy Payment has been cancelled and the transaction has been declined.");
		$this->_redirect('checkout/onepage/failure');
    }

    /**
     * when Paysbuy returns
     * The order information at this point is in POST
     * variables.  However, you don't want to "process" the order until you
     * get validation from the IPN.
     */
    public function  successAction()
    {
        if (!$this->getRequest()->isPost()) {
        	throw new Exception(' Wrong request type:  should be Post.', 10);
        }

        $status = true;

		$response = $this->getRequest()->getPost();		 		
		if (empty($response))  {
            $status = false;
			throw new Exception('Response doesn\'t contain GET /POST elements.', 20);
        }
		
		
		
		// //=> Paysbuy Return Data
		// [result] => 00ORD-16-04-04-00000014
	 //    [apCode] => 109343
	 //    [amt] => 8595.00
	 //    [fee] => 322.3149
	 //    [method] => 02
	 //    [create_date] => 4/4/2559 17:00:35
	 //    [payment_date] => 4/4/2559 17:01:21
		//-> result = 00 = Success, 99 Fail , 22 = 02 processing ResultCode + Invoice 
		//=> Invoice = �Ţ������觫����Թ���
		//=> apCode = Transaction Code �ͧ Paysbuy  / Approved Code, 000000 = ��������
		//=> amt = �ӹǹ�Թ������¡��
		//=> fee = ��Ҹ���������÷���¡��
		//=> method =��ͧ�ҧ��ê���
		$PaysbuyResult = ""; 
		$PaysbuyResultCode = ""; 
		$PaysbuyInvoice = "";
		$PaysbuyAppCode = "";
		$PaysbuyAmount = 0;
		$PaysbuyFee = 0;
		$PaysbuyMethod = "";
		
		$PaysbuyCreateDate = "";
		$PaysbuyPaymentDate = "";
		$confirmCS = "";
		
		//print_r($response);
		
		if(isset($response["result"])) $PaysbuyResult = trim($response["result"]);
		if(isset($response["apCode"])) $PaysbuyAppCode=$response["apCode"];
		if(isset($response["amt"])) $PaysbuyAmount =$response["amt"];
		if(isset($response["fee"])) $PaysbuyFee= $response["fee"];
		if(isset($response["method"])) $PaysbuyMethod=$response["method"];
		
		if(isset($response["create_date"])) $PaysbuyCreateDate = $response["create_date"];
		if(isset($response["payment_date"])) $PaysbuyPaymentDate = $response["payment_date"];
		if(isset($response["confirm_cs"])) $confirmCS = $response["confirm_cs"];
		
		//=> Data for show paysbuy return message
		$PaysbuyReturnData = "Result : " . $PaysbuyResult . "\n<br/>" .
			"Approve Code : " . $PaysbuyAppCode . "\n<br/>" .
			"Paid Amount : " . $PaysbuyAmount . "\n<br/>" .
			"Fee : " . $PaysbuyFee . "\n<br/>" .
			"Method : " . $PaysbuyMethod .  "\n<br/>" . 
			"Create Date : " . $PaysbuyCreateDate .  "\n<br/>" . 
			"Paid Date : " . $PaysbuyPaymentDate . "\n\<br/>" .
			"confirm CS : " . $confirmCS . "\n\<br/>";
		// echo "<p>My Data : $PaysbuyReturnData</p>";
		

		if ($PaysbuyResult !=="") {
			//=> �ó��ա�� Return ��ҡ�Ѻ��
			$PaysbuyResultCode = substr($PaysbuyResult,0,2); 
			$PaysbuyInvoice = substr($PaysbuyResult,2);
			//echo "<br/>Order_Id=". $PaysbuyInvoice . " and Payment Result : " . $PaysbuyResultCode;

			$order = Mage::getModel('sales/order');
			$order->loadByIncrementId($PaysbuyInvoice);
			
			if ($order->getId()) {		

				$response = Mage::helper('Paysbuy')->checkTransection($PaysbuyInvoice);
				foreach ($response as $key => $value) {
					$PaysbuyReturnData .=$key .' : '.$value . "\n\<br/>" ;
				}

				$CurrentOrderStatus = $order->getStatus();
				$CurrentOrderState = $order->getState();
				
				$PaysbuyReturnData = "- **** Paysbuy click back || ". $CurrentOrderState . "=>" . $CurrentOrderStatus ." **** \n<br/>" . $PaysbuyReturnData;
				
				$order->addStatusToHistory($order->getStatus(), $PaysbuyReturnData, false);
				$order->save();				
				
				$dbAmt = sprintf('%.2f', $order->getGrandTotal());
				
				switch ($response['result']) {
					case "00":
						if ($dbAmt == $PaysbuyAmount) {
							$comment = "Received through Paysbuy Payment: " . $dbAmt;
							if ($order->getState() ==Mage::getStoreConfig('payment/Paysbuy/order_status') ) {							
								$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, "New : " .  $comment, 1)->save(); 		
								$order->setStatus(Mage::getStoreConfig('payment/Paysbuy/payment_success_status'), true, "New : " .  $comment, 1)->save();
								//=> auto invoice
								$isAutoCreateInvoice = Mage::getStoreConfig('payment/Paysbuy/autocreateinvoice');
								if (((int)$isAutoCreateInvoice==1) && ($order->canInvoice()))  {

									//START Handle Invoice
									$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
									$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
									$invoice->register();
									$invoice->getOrder()->setCustomerNoteNotify(true);
									$invoice->getOrder()->setIsInProcess(true);
									$invoice->sendEmail();
									$transactionSave = Mage::getModel('core/resource_transaction')
									->addObject($invoice)
									->addObject($invoice->getOrder());
									$transactionSave->save();

									//ICC-142 - Harry - Save Invoice Increment Id For Online Payment
									// Mage::helper('invoiceid')->saveInvoiceIncrementId($order, $invoice);

									$order->addStatusToHistory($order->getStatus(), "Automatically invoiced", false);
									$order->save();
									//END Handle Invoice																
								}
								
								//=> end uto invoice
								
								$order->sendOrderUpdateEmail(true, $comment);
								$session = Mage::getSingleton('checkout/session');
								$session->setQuoteId($session->getPaysbuyStandardQuoteId(true));
							}
							else {
								$order->addStatusToHistory($order->getStatus(), strtolower($CurrentOrderState) . " -> " . $PaysbuyReturnData, false);
								$order->save();								
							}
							/**
							* set the quote as inactive after back from Paysbuy
							*/
						}
						else {
							$order->addStatusToHistory($order->getStatus(), $dbAmt . "<>" . $PaysbuyAmount . "->" . $PaysbuyReturnData, false);
							$order->save();

						}
							
						Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
						$this->_redirect('checkout/onepage/success', array('_secure'=>true));
							
						break;
					case "02":
						$comment = "Awaiting Counter Service payment";
						// echo "<pre>";
						// print_r(Mage::getStoreConfig('payment/Paysbuy/order_status'));
						// echo "</pre>";
						// $order->setState(Mage::getStoreConfig('payment/Paysbuy/order_status'), true, $comment, 1)->save();
						$order->setStatus(Mage::getStoreConfig('payment/Paysbuy/payment_success_status_counter'), true, $comment, 1)->save();
						$this->getCheckout()->setPaysbuyErrorMessage('Awaiting Counter Service payment');
						//$order->sendOrderUpdateEmail(true, $comment);
						// $order->sendNewOrderEmail();
						$this->_redirect('checkout/onepage/success', array('_secure'=>true));
						break;
					case "99":
						$comment = "Payment Failed";
						if($order->canCancel()) {
							$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, $comment, 1)->save();
						}
						
						$this->getCheckout()->setPaysbuyErrorMessage('An error occurred in the process of payment');
						//$order->sendOrderUpdateEmail(true, $comment);
						$this->_redirect('checkout/cart');
						break;
					default:
						$comment = "Other - " . $PaysbuyResultCode;
						if($order->canCancel()) {
							$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, $comment, 1)->save();
						}
						
						$this->getCheckout()->setPaysbuyErrorMessage('Paysbuy can not payment');
						$this->_redirect('checkout/cart');
						
						break;
				}									
			}
			else {
				//=>
				//=> �ó�����ա��  Can not load order
				$this->getCheckout()->setPaysbuyErrorMessage('Paysbuy can not load order.('. $PaysbuyInvoice .')'  );   
				$this->cancelAction();
				return false;						
				//>
				
			}
		}
		else
		{
			//=> �ó�����ա�� Return Result Code
			$this->getCheckout()->setPaysbuyErrorMessage('Paysbuy not affect the transaction back.('. $PaysbuyReturnData .')'  );   
			$this->cancelAction();
			return false;		
		}		
    }
	
    public function  feedAction()
    {
		Mage::log('feed start', null, 'mylogfile.log');
        if (!$this->getRequest()->isPost()) {
        	throw new Exception(' Wrong request type:  should be Post.', 10);
        }

        $status = true;

		$response = $this->getRequest()->getPost();		 		
		if (empty($response))  {
            $status = false;
			throw new Exception('Response doesn\'t contain GET /POST elements.', 20);
        }
		
		
		
		//=> Paysbuy Return Data
		//-> result = 00 = �����, 99 ��������, 22 = ���������ҧ���Թ��� �ٻẺ��� ResultCode + Invoice 
		//=> Invoice = �Ţ������觫����Թ���
		//=> apCode = Transaction Code �ͧ Paysbuy  / Approved Code, 000000 = ��������
		//=> amt = �ӹǹ�Թ������¡��
		//=> fee = ��Ҹ���������÷���¡��
		//=> method =��ͧ�ҧ��ê���
		$PaysbuyResult = ""; 
		$PaysbuyResultCode = ""; 
		$PaysbuyInvoice = "";
		$PaysbuyAppCode = "";
		$PaysbuyAmount = 0;
		$PaysbuyFee = 0;
		$PaysbuyMethod = "";
		
		$PaysbuyCreateDate = "";
		$PaysbuyPaymentDate = "";
		$confirmCS = "";
		
		//print_r($response);
		
		if(isset($response["result"])) $PaysbuyResult = trim($response["result"]);
		if(isset($response["apCode"])) $PaysbuyAppCode=$response["apCode"];
		if(isset($response["amt"])) $PaysbuyAmount =$response["amt"];
		if(isset($response["fee"])) $PaysbuyFee= $response["fee"];
		if(isset($response["method"])) $PaysbuyMethod=$response["method"];
		
		if(isset($response["create_date"])) $PaysbuyCreateDate = $response["create_date"];
		if(isset($response["payment_date"])) $PaysbuyPaymentDate = $response["payment_date"];
		if(isset($response["confirm_cs"])) $confirmCS = $response["confirm_cs"];
		
		//=> Data for show paysbuy return message
		$PaysbuyReturnData = "Result : " . $PaysbuyResult . "\n<br/>" .
			"Approve Code : " . $PaysbuyAppCode . "\n<br/>" .
			"Paid Amount : " . $PaysbuyAmount . "\n<br/>" .
			"Fee : " . $PaysbuyFee . "\n<br/>" .
			"Method : " . $PaysbuyMethod .  "\n<br/>" . 
			"Create Date : " . $PaysbuyCreateDate .  "\n<br/>" . 
			"Paid Date : " . $PaysbuyPaymentDate . "\n\<br/>" .
			"confirm CS : " . $confirmCS;
		//echo "<p>My Data : $PaysbuyReturnData</p>";
		
		Mage::log('My log entry : ' . $PaysbuyReturnData, null, 'mylogfile.log');
		
		if ($PaysbuyResult !=="") {
			//=> �ó��ա�� Return ��ҡ�Ѻ��
			$PaysbuyResultCode = substr($PaysbuyResult,0,2); 
			$PaysbuyInvoice = substr($PaysbuyResult,2);
			//echo "<br/>Order_Id=". $PaysbuyInvoice . " and Payment Result : " . $PaysbuyResultCode;
			$order = Mage::getModel('sales/order');
			$order->loadByIncrementId($PaysbuyInvoice);
			
			if ($order->getId()) {				
				
				$response = Mage::helper('Paysbuy')->checkTransection($PaysbuyInvoice);
				foreach ($response as $key => $value) {
					$PaysbuyReturnData .=$key .' : '.$value . "\n\<br/>" ;
				}

				$CurrentOrderStatus = $order->getStatus();
				$CurrentOrderState = $order->getState();
				
				$PaysbuyReturnData = "- **** Paysbuy background response || ". $CurrentOrderState . "=>" . $CurrentOrderStatus ." **** \n<br/>" . $PaysbuyReturnData;
				
				$order->addStatusToHistory($order->getStatus(), $PaysbuyReturnData, false);
				$order->save();				
				
				
				$dbAmt = sprintf('%.2f', $order->getGrandTotal());
				switch ($response['result']) {
					case "00":
						if ($dbAmt == $PaysbuyAmount) {
							$comment = "Received through Paysbuy Payment: " . $dbAmt;
							if (strtolower($CurrentOrderState)=="new") {							
								$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, "New : " .  $comment, 1)->save(); 		
								$order->setStatus(Mage::getStoreConfig('payment/Paysbuy/payment_success_status'), true, "New : " .  $comment, 1)->save();
								//=> auto invoice
								$isAutoCreateInvoice = Mage::getStoreConfig('payment/Paysbuy/autocreateinvoice');
								if (((int)$isAutoCreateInvoice==1) && ($order->canInvoice()))  {
									//START Handle Invoice
									$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
									$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
									$invoice->register();
									$invoice->getOrder()->setCustomerNoteNotify(true);
									$invoice->getOrder()->setIsInProcess(true);
									$invoice->sendEmail();
									$transactionSave = Mage::getModel('core/resource_transaction')
									->addObject($invoice)
									->addObject($invoice->getOrder());
									$transactionSave->save();
								
									$order->addStatusToHistory($order->getStatus(), "Automatically invoiced", false);
									$order->save();
									//END Handle Invoice																
								}
								
								//=> end uto invoice
																
								// $order->sendOrderUpdateEmail(true, $comment);
								$session = Mage::getSingleton('checkout/session');
								$session->setQuoteId($session->getPaysbuyStandardQuoteId(true));
							}
							else {
								$order->addStatusToHistory($order->getStatus(), strtolower($CurrentOrderState) . " -> " . $PaysbuyReturnData, false);
								$order->save();								
							}
							/**
							* set the quote as inactive after back from Paysbuy
							*/
						}
						else {
							$order->addStatusToHistory($order->getStatus(), $dbAmt . "<>" . $PaysbuyAmount . "->" . $PaysbuyReturnData, false);
							$order->save();

						}
							
						Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
						$this->_redirect('checkout/onepage/success', array('_secure'=>true));
							
						break;
					case "02":
						$comment = "Awaiting Counter Service payment";
						// echo "<pre>";
						// print_r(Mage::getStoreConfig('payment/Paysbuy/order_status'));
						// echo "</pre>";
						// $order->setState(Mage::getStoreConfig('payment/Paysbuy/order_status'), true, $comment, 1)->save();
						$order->setStatus(Mage::getStoreConfig('payment/Paysbuy/payment_success_status_counter'), true, $comment, 1)->save();
						// $this->getCheckout()->setPaysbuyErrorMessage('Awaiting Counter Service payment');
						// $order->sendOrderUpdateEmail(true, $comment);
						break;
					case "99":
						$comment = "Payment Failed";
						if($order->canCancel()) {
							$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, $comment, 1)->save();
						}
						
						$this->getCheckout()->setPaysbuyErrorMessage('An error occurred in the process of payment');
						//$order->sendOrderUpdateEmail(true, $comment);
						$this->_redirect('checkout/cart');
						break;
					default:
						$comment = "Other - " . $PaysbuyResultCode;
						if($order->canCancel()) {
							$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, $comment, 1)->save();
						}
						
						$this->getCheckout()->setPaysbuyErrorMessage('Paysbuy can not payment');
						$this->_redirect('checkout/cart');
						
						break;
				}									
			}
			else {
				//=>
				//=> �ó�����ա��  Can not load order
				$this->getCheckout()->setPaysbuyErrorMessage('Paysbuy can not load order.('. $PaysbuyInvoice .')'  );   
				$this->cancelAction();
				return false;						
				//>
				
			}
		}
		else
		{
			//=> �ó�����ա�� Return Result Code
			$this->getCheckout()->setPaysbuyErrorMessage('Paysbuy not affect the transaction back.('. $PaysbuyReturnData .')'  );   
			$this->cancelAction();
			return false;		
		}		
    }
		
	public function errorAction()
    {
        $this->_redirect('checkout/onepage/');
    }
     /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

}
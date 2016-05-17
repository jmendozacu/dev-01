<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mgf_KSmartpay	
 * @author	   cherdchai Hinjumpa , bugcherd@gmail.com   089-003-5240
 * @website   http://magentothai.wordpress.com/
 */


/**
 * KSmartpay Standard Checkout Controller
 *
 * @category   Mage
 * @package    Mgf_KSmartpay
 * @author      Magento Core Team <core@magentocommerce.com>
 */
      
 
class Mgf_KSmartpay_KSmartpayController extends Mage_Core_Controller_Front_Action
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
     * Get singleton with KSmartpay strandard order transaction information
     *
     * @return Mgf_KSmartpay_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('KSmartpay/standard');
    }

    /**
     * When a customer chooses KSmartpay on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
		
		$session = Mage::getSingleton('checkout/session');
		$session->setKSmartpayStandardQuoteId($session->getQuoteId());
		$order = Mage::getModel('sales/order');
		$order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
		$order->sendNewOrderEmail();
		$order->save();
		
		$this->getResponse()->setBody($this->getLayout()->createBlock('KSmartpay/form_redirect')->toHtml());
		$session->unsQuoteId();

    }

    /**
     * When a customer cancel payment from KSmartpay.
     */
    public function cancelAction($OrderID, $APICall, $CancelResult, $ResponseMessage)
    {
		$ResponseMessge = "";
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getKSmartpayStandardQuoteId(true));
		
		$order = Mage::getModel('sales/order');
		$order->loadByIncrementId($OrderID);
		
		$payment = $order->getPayment();
		$payment->setAdditionalInformation('approvecode',"Cancel");
		$payment->setAdditionalInformation('paidnote',$CancelResult);
		$payment->save();

		$state = Mage_Sales_Model_Order::STATE_CANCELED;
		$ResponseMessge = $order->getStatus() . " -> "  . $state;
		if($order->canCancel()) {
			$message=Mage::helper('KSmartpay')->__("Cancelled ::: " .  $ResponseMessage);
			$order->cancel();
			$order->setState($state, true, $message);
			$order->save();
		}
		else {
			$ResponseMessge .= " (Order can not cancel)";
			$message=Mage::helper('KSmartpay')->__("Order can not cancel :: ". $order->getStatus() ." (". $ResponseMessage .").");
			$order->addStatusToHistory($order->getStatus(), $message, false);	
			$order->save();		
		}
		
		
		if ($APICall =="bjc") {
			echo $ResponseMessge;
		}
		else {
			Mage::getSingleton('checkout/session')->addError("Installment Payment has been cancelled and the transaction has been declined.");
			$this->_redirect('checkout/cart');		
		}
    }

    /**
     * when KSmartpay returns
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
        // var_dump($response);
        // exit();
		$Paidby = "";
		if(isset($response["paidsrc"])) $Paidby  =$response["paidsrc"];
		
		echo "<p>paid by : " . $Paidby  ."</p>";
		//exit;
		
		
		switch ($Paidby) {
		    case "krungsri":
		  //       /************************ Krungsri***********************/
				// $ReturnData = "";
				// $PaidType = "";				
				// $ReturnCode = "";
				// $ReturnMessage = "";
				// $ReturnMerchantID = "";
				// $ReturnOrderNo = "";
				// if(isset($response["rptsrc"])) $PaidType =$response["rptsrc"];
				// if(isset($response["order_no"])) $ReturnOrderNo =$response["order_no"];
				// if(isset($response["mid"])) $ReturnMerchantID=$response["mid"];
				// if(isset($response["return"])) $ReturnCode =$response["return"];		
				// if(isset($response["msg"])) $ReturnMessage= $response["msg"];
				
				
				// //=> Data for show KSmartpay return message
				// $ReturnData = "Reply Back = ". $PaidType ."\n\r" .  
				// 	"Response code = ". $ReturnCode ."\n\r" .  
				// 	"Response message = ". $ReturnMessage ."\n\r" .  
				// 	"Merchant ID = ". $ReturnMerchantID ."\n\r" .  
				// 	"Merchant order no = ". $ReturnOrderNo ."\n\r";
				
				// echo $ReturnOrderNo . "|" . $ReturnMerchantID .   "|" . $ReturnCode .  "|" . $ReturnMessage;
				
				// $ResponseMessge = "";
				
				// $order = Mage::getModel('sales/order');
				// $order->loadByIncrementId($ReturnOrderNo);
				
				// if (strtolower($order->getStatus()) == Mage::getStoreConfig('payment/KSmartpay/order_status')) {
				// 	//=> Pending
				// 	if (($ReturnMessage=="success") && ($ReturnCode=="1")) {
				// 		//=> Paid
				// 		$state = Mage::getStoreConfig('payment/KSmartpay/payment_success_status');
				// 		$payment = $order->getPayment();
				// 		$ResponseMessge = $order->getStatus() . " -> "  . $state;
					
				// 		$message=Mage::helper("KBank")->__("Your payment is authorized by ". $Paidby ."  (". $ReturnData .").");
				// 		$order->setState($state, true, $message);
				// 		$order->save();
						
				// 		//=> Save Payment Information
				// 	    //$payment->setAdditionalInformation('approvecode',$KBankAuthorizeCode);
				// 		//$payment->setAdditionalInformation('kresponsecode',$KBankResultCode);
				// 		//$payment->setAdditionalInformation('kcardno',$KBankCardNo);
				// 		//$payment->setAdditionalInformation('kcardtype',$KBankFILLSPACE);
				// 		//$payment->setAdditionalInformation('kamount',$KBankAmount);
				// 		//$payment->setAdditionalInformation('kbthamount',$KBankTHBAmount);
				// 		//$payment->setAdditionalInformation('kcurrencycode',$KBankCURISO);
				// 		//$payment->setAdditionalInformation('kcurrencyrate',$KBankFXRATE);
				// 		//$payment->setAdditionalInformation('paidnote',$TranPaidNote);					
				// 		//$payment->save();				
					
				// 		$session = Mage::getSingleton('checkout/session');
				// 		$session->setQuoteId($session->getKSmartpayStandardQuoteId(true));
				// 		/**
				// 		* set the quote as inactive after back from KBank
				// 		*/
				// 		Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
				// 		$this->_redirect('checkout/onepage/success', array('_secure'=>true));
				// 	}
				// 	else {
				// 		//=> cancel
				// 		$session = Mage::getSingleton('checkout/session');
				// 		$session->setQuoteId($session->getKSmartpayStandardQuoteId(true));
				// 		/**
				// 		* set the quote as inactive after back from KBank
				// 		*/
				// 		Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();				
				// 		Mage::getSingleton('checkout/session')->addSuccess($Paidby . " Payment has been cancelled and the transaction has been declined.");
						
				// 		//echo $MerchantInvoiceNo . ","  .  $BJCCallType . "," .  $TranPaidNote . ", " . $KBankReturnData;
				// 		//exit;
						
				// 		$this->cancelAction($ReturnOrderNo,"web", "" ,$ReturnData);				
				// 		return false;						
						
				// 		//=> cancel
					
				// 	}
				// 	//=> Pending - End
				// }
				// else {
				// 	//=> Other Status
				// 	$ResponseMessge = $order->getStatus() . " -> Add order history";
				// 	$message=Mage::helper('KBank')->__("Your payment is authorized by ". $Paidby ." (". $ReturnData .").");
				// 	$order->addStatusToHistory($order->getStatus(), $message, false);
				// 	$order->save();

				// 	//Mage::getSingleton('checkout/session')->addError($Paidby  . " Payment has been cancelled and the transaction has been declined.");
				// 	$this->_redirect('checkout/cart');		
				// 	//=> Other Status - End				
				// }
				
				
				
		        break;
		   default:
		        //=> SmartPay by kbank
				//=> KSmartpay Return Data
				//-> result = 00 = Approved, 01 = Other > Not Approved
				$KSmartpayReturnData  = "";
				$KSmartpayResultCode = ""; 
				$KSmartpayAuthorizeCode = "";
				$KSmartpayInvoice = "";
				$KSmartpayCardNo = 0;
				
				$KSmartpayAmount = 0;
				$KSmartpayTHBAmount = 0;
				
				$KSmartpayCURISO = "";
				$KSmartpayFXRATE = "";
				$KSmartpayFILLSPACE = "";
				
				$KSmartpayMID = "";
				$KSmartpayPLANID ="";
				$KSmartpayPAYMONTH = "";
				$KSmartpayINTTYPE= "";
				$KSmartpayINTRATE = "";
				$KSmartpayAMTPERMONTH = "";
				$KSmartpayTOTALAMT = "";
				$KSmartpayMANGFEE = "";
				$KSmartpayINTMODE = "";
				
				$KSmartpayMD5CHECKSUM = "";		
				$OnsiteMD5CHECKSUM = "";
				
				
				if(isset($response["HOSTRESP"])) $KSmartpayResultCode =$response["HOSTRESP"];
				if(isset($response["AUTHCODE"])) $KSmartpayAuthorizeCode=$response["AUTHCODE"];
				if(isset($response["RETURNINV"])) $KSmartpayInvoice =$response["RETURNINV"];		
				if(isset($response["CARDNUMBER"])) $KSmartpayCardNo= $response["CARDNUMBER"];
				
				if(isset($response["AMOUNT"])) $KSmartpayAmount= $response["AMOUNT"];
				if(isset($response["THBAMOUNT"])) $KSmartpayTHBAmount= $response["THBAMOUNT"];
				
				if(isset($response["CURISO"])) $KSmartpayCURISO= $response["CURISO"];
				if(isset($response["FXRATE"])) $KSmartpayFXRATE= $response["FXRATE"];
				if(isset($response["FILLSPACE"])) $KSmartpayFILLSPACE= $response["FILLSPACE"];
				
				if(isset($response["MID"])) $KSmartpayMID= $response["MID"];
				if(isset($response["PLANID"])) $KSmartpayPLANID= $response["PLANID"];
				if(isset($response["PAYMONTH"])) $KSmartpayPAYMONTH= $response["PAYMONTH"];
				if(isset($response["INTRATE"])) $KSmartpayINTRATE= $response["INTRATE"];
				if(isset($response["AMTPERMONTH"])) $KSmartpayAMTPERMONTH= $response["AMTPERMONTH"];
				
				if(isset($response["TOTALAMT"])) $KSmartpayTOTALAMT= $response["TOTALAMT"];
				if(isset($response["MANGFEE"])) $KSmartpayMANGFEE= $response["MANGFEE"];
				if(isset($response["INTMODE"])) $KSmartpayINTMODE= $response["INTMODE"];
		
				if(isset($response["MD5CHECKSUM"])) $KSmartpayMD5CHECKSUM= $response["MD5CHECKSUM"];
				
				$OnsiteMD5CHECKSUM = "";
				
				//=> Data for show KSmartpay return message
				$KSmartpayReturnData = "Response Code = ". $KSmartpayResultCode ."\n\r" .  
						"Authorize Code = ". $KSmartpayAuthorizeCode ."\n\r" . 
						"Invoice No = ". $KSmartpayInvoice ."\n\r" . 
						"Credit Card Number = ". $KSmartpayCardNo ."\n\r" . 
						"Transaction Amount = ". $KSmartpayAmount ."\n\r" . 
						"Amount that merchant posts on Web site = ". $KSmartpayTHBAmount ."\n\r" . 
						"Transaction currency = ". $KSmartpayCURISO ."\n\r" . 
						"Exchange rate of transaction = ". $KSmartpayFXRATE ."\n\r" . 
						"Type of Card = ". $KSmartpayFILLSPACE ."\n\r";
				if (Mage::getStoreConfig('payment/KSmartpay/smartpayactive')=="1") {
					$KSmartpayReturnData .= "MID = ". $KSmartpayMID ."\n\r" .  
						"Plan ID Value for Smartpay = ". $KSmartpayPLANID ."\n\r" . 
						"Number of month for installment = ". $KSmartpayPAYMONTH ."\n\r" . 
						"Interest calculation method for smart payment = ". $KSmartpayINTTYPE ."\n\r" . 
						"Interest rate charged for smart payment per month = ". $KSmartpayINTRATE ."\n\r" . 
						"Amount payment per Month for smart payment = ". $KSmartpayAMTPERMONTH ."\n\r" . 
						"Total amount include Interest amount for smart payment = ". $KSmartpayTOTALAMT ."\n\r" . 
						"Management Fee for smart payment = ". $KSmartpayFXRATE ."\n\r" . 
						"Mode for Interest payment acceptance = ". $KSmartpayINTMODE ."\n\r";		
				}
				
				$KSmartpayReturnData .= "MD5CHECKSUM = ". $KSmartpayMD5CHECKSUM ."\n\r" .  
										" Calulate MD5CHECKSUM = ". $KSmartpayResultCode ."\n\r";
		
				//echo "<p>My Data : $KSmartpayReturnData</p>";
		
					if($KSmartpayResultCode=="00")
					{
						$MerchantInvoiceNo = substr($KSmartpayInvoice,-9);
						
						$order = Mage::getModel('sales/order');
						$order->loadByIncrementId($MerchantInvoiceNo);
						$state = Mage::getStoreConfig('payment/KSmartpay/payment_success_status');
						$message=Mage::helper('KSmartpay')->__('Your payment is authorized by KSmartpay ('. $KSmartpayReturnData .').');
						$order->setState($state, true, $message);
						$order->save();
						
						//=> Create Invoice
			            if (Mage::getStoreConfig('payment/KSmartpay/payment_autoinvoice')=="1") {
			                $invoice = Mage::getModel('sales/service_order',$order)->prepareInvoice();
			                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
			                $invoice->register();
			                $transactionSave = Mage::getModel('core/resource_transaction')
			                    ->addObject($invoice)
			                    ->addObject($invoice->getOrder());
			                $transactionSave->save();
			            }
						//=> End Create Invoice
						
						$session = Mage::getSingleton('checkout/session');
						$session->setQuoteId($session->getKSmartpayStandardQuoteId(true));
						/**
						* set the quote as inactive after back from KSmartpay
						*/
						Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
						$this->_redirect('checkout/onepage/success', array('_secure'=>true));
					}
					else
					{
						$this->getCheckout()->setKSmartpayErrorMessage('KSmartpay UNSUCCESS ('. $KSmartpayReturnData .')');   
						$this->cancelAction();
						return false;
					}				
				
				
				
				//=> SmartPay by kbank end 
		        break;
		}		
		
		
		
    }


    //For Krungsri get data from post
    public function foregroundAction(){
		// var_dump($this->getRequest()->getParams());
		
		$secretKey = Mage::getStoreConfig('payment/KSmartpay/krungsrisecretkey');
		$orderID = $this->getRequest()->getParam('order_no');
		$mid = Mage::getStoreConfig("payment/KSmartpay/krungsrimerchantno");

		$url = Mage::getStoreConfig("payment/KSmartpay/krungsrigatewayurl")."/checkPayment?mid=$mid&api_secret=$secretKey&order_no=$orderID";
		// $content = file_get_contents($urls);
		// $content = json_decode($content,true);

		$rest = $this->get_web_page($url);
		
		$response = json_decode($rest['content'],true);
		

		if($response['header']['msg']=="success"){
			//success
			/*
			[body] => Array
	        (
	            [mid] => 450000
	            [order_no] => 1605110045
	            [ref_no] => 08
	            [t_date] => 2016-05-11 15:43:32.755
	            [approval_code] => 946366
	            [slip_no] => ND000076
	            [card] => 494351xxxxxx2910
	            [total_price] => 14873
	            [interest1] => 0
	            [interest2] => 0
	            [cuc1] => 0
	            [cuc2] => 0
	            [term1] => 10
	            [term2] => 0
	            [total_amt] => 14873
	            [install_amt1] => 1487.3
	            [install_amt2] => 0
	        )*/
	        	$order = Mage::getModel('sales/order');
				$order->loadByIncrementId($response['body']['order_no']);
				if (strtolower($order->getStatus()) == Mage::getStoreConfig('payment/KSmartpay/order_status')) {

					$state = Mage::getStoreConfig('payment/KSmartpay/payment_success_status');
					$payment = $order->getPayment();
				
					$ReturnData = "Foreground Response <br> Your payment is authorized by Krungsri<br>".  
							"Approval Code = ". $response['body']['approval_code'] ."<br>" . 
							"Slip No = ". $response['body']['slip_no'] ."<br>" . 
							"Credit Card Number = ". $response['body']['card'] ."<br>" . 
							"Transaction Date = ". $response['body']['t_date'] ."<br>" . 
							"Total Price = ". $response['body']['total_price'] ."<br>" . 
							"Interest1 = ". $response['body']['interest1'] ."<br>" . 
							"term1 = ". $response['body']['term1'] ."<br>" . 
							"Install_amt1 = ". $response['body']['install_amt1'] ."<br>";
					$order->setState($state, true, $ReturnData);
					$order->save();

					//=> Create Invoice
		            if (Mage::getStoreConfig('payment/KSmartpay/payment_autoinvoice')=="1") {
		                $invoice = Mage::getModel('sales/service_order',$order)->prepareInvoice();
		                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
		                $invoice->register();
		                $transactionSave = Mage::getModel('core/resource_transaction')
		                    ->addObject($invoice)
		                    ->addObject($invoice->getOrder());
		                $transactionSave->save();
		            }

					$session = Mage::getSingleton('checkout/session');
					$session->setQuoteId($session->getKSmartpayStandardQuoteId(true));
					/**
					* set the quote as inactive after back from KBank
					*/
					Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
					$this->_redirect('checkout/onepage/success', array('_secure'=>true));
				} else {
					//=> Other Status
					$ReturnData = "Foreground Response <br> ====".$order->getStatus()."==== <br> Your payment is authorized by Krungsri<br>".  
							"Approval Code = ". $response['body']['approval_code'] ."<br>" . 
							"Slip No = ". $response['body']['slip_no'] ."<br>" . 
							"Credit Card Number = ". $response['body']['card'] ."<br>" . 
							"Transaction Date = ". $response['body']['t_date'] ."<br>" . 
							"Total Price = ". $response['body']['total_price'] ."<br>" . 
							"Interest1 = ". $response['body']['interest1'] ."<br>" . 
							"term1 = ". $response['body']['term1'] ."<br>" . 
							"Install_amt1 = ". $response['body']['install_amt1'] ."<br>";
					$order->addStatusToHistory($order->getStatus(), $ReturnData, false);
					$order->save();

					//Mage::getSingleton('checkout/session')->addError($Paidby  . " Payment has been cancelled and the transaction has been declined.");
					// $this->_redirect('checkout/cart');		
					$this->_redirect('checkout/onepage/success', array('_secure'=>true));
					//=> Other Status - End		
				}
		} else {
			//Fail
			$order = Mage::getModel('sales/order');
			$order->loadByIncrementId($orderID);
			$state = Mage_Sales_Model_Order::STATE_CANCELED;
			if($order->getId()){
				$ResponseMessage = $order->getStatus() . " -> "  . $state;
				if($order->canCancel()) {
					$message=Mage::helper('KSmartpay')->__("Cancelled ::: " .  $ResponseMessage);
					$order->cancel();
					$order->setState($state, true, $message);
					$order->save();
				}
				else {
					$ResponseMessage .= " (Order can not cancel)";
					$message=Mage::helper('KSmartpay')->__("Order can not cancel :: ". $order->getStatus() ." (". $ResponseMessage .").");
					$order->addStatusToHistory($order->getStatus(), $message, false);	
					$order->save();		
					Mage::getSingleton('checkout/session')->addError(" Payment has been cancelled and the transaction has been declined.");
					$this->_redirect('checkout/cart');
				}	
			} else {
				Mage::getSingleton('checkout/session')->addError(" Payment has been cancelled and the transaction has been declined.");
				$this->_redirect('checkout/cart');		
			}
		}
    }
    //For Krungsri
    public function backgroundAction(){
    	if (!$this->getRequest()->isPost()) {
        	throw new Exception(' Wrong request type:  should be Post.', 10);
        }
      
        $status = true;

		$response = $this->getRequest()->getPost();		 		
		if (empty($response))  {
            $status = false;
        	throw new Exception('Response doesn\'t contain GET /POST elements.', 20);
        }

        $order = Mage::getModel('sales/order');
		$order->loadByIncrementId($response['order_no']);
		if($order->getId()){
			if($response['msg'] == 'success'){
				if (strtolower($order->getStatus()) == Mage::getStoreConfig('payment/KSmartpay/order_status')) {

					$state = Mage::getStoreConfig('payment/KSmartpay/payment_success_status');
					$payment = $order->getPayment();
				
					$ReturnData = "Background Response <br> Your payment is authorized by Krungsri<br>".  
							"Approval Code = ". $response['approval_code'] ."<br>" . 
							"Slip No = ". $response['slip_no'] ."<br>" . 
							"Credit Card Number = ". $response['card'] ."<br>" . 
							"Transaction Date = ". $response['t_date'] ."<br>" . 
							"Total Price = ". $response['total_price'] ."<br>" . 
							"Interest1 = ". $response['interest1'] ."<br>" . 
							"term1 = ". $response['term1'] ."<br>" . 
							"Install_amt1 = ". $response['install_amt1'] ."<br>";
					$order->setState($state, true, $ReturnData);
					$order->save();

					//=> Create Invoice
		            if (Mage::getStoreConfig('payment/KSmartpay/payment_autoinvoice')=="1") {
		                $invoice = Mage::getModel('sales/service_order',$order)->prepareInvoice();
		                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
		                $invoice->register();
		                $transactionSave = Mage::getModel('core/resource_transaction')
		                    ->addObject($invoice)
		                    ->addObject($invoice->getOrder());
		                $transactionSave->save();
		            }

					$session = Mage::getSingleton('checkout/session');
					$session->setQuoteId($session->getKSmartpayStandardQuoteId(true));
					/**
					* set the quote as inactive after back from KBank
					*/
					Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
					$this->_redirect('checkout/onepage/success', array('_secure'=>true));
				} else {
					//=> Other Status
					$ReturnData = "Background Response <br> ====".$order->getStatus()."==== <br> Your payment is authorized by Krungsri<br>".  
							"Approval Code = ". $response['body']['approval_code'] ."<br>" . 
							"Slip No = ". $response['body']['slip_no'] ."<br>" . 
							"Credit Card Number = ". $response['body']['card'] ."<br>" . 
							"Transaction Date = ". $response['body']['t_date'] ."<br>" . 
							"Total Price = ". $response['body']['total_price'] ."<br>" . 
							"Interest1 = ". $response['body']['interest1'] ."<br>" . 
							"term1 = ". $response['body']['term1'] ."<br>" . 
							"Install_amt1 = ". $response['body']['install_amt1'] ."<br>";
					$order->addStatusToHistory($order->getStatus(), $ReturnData, false);
					$order->save();

					//Mage::getSingleton('checkout/session')->addError($Paidby  . " Payment has been cancelled and the transaction has been declined.");
					$this->_redirect('checkout/cart');		
					//=> Other Status - End		
				}
			} else {
				$state = Mage_Sales_Model_Order::STATE_CANCELED;
				$ResponseMessage = $order->getStatus() . " -> "  . $state;
				if($order->canCancel()) {
					$message=Mage::helper('KSmartpay')->__("Cancelled ::: " .  $ResponseMessage);
					$order->cancel();
					$order->setState($state, true, $message);
					$order->save();
				}
			}
		}
    }

    private function get_web_page( $url )
	{
	    $options = array(
	        CURLOPT_RETURNTRANSFER => true,     // return web page
	        CURLOPT_HEADER         => false,    // don't return headers
	        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
	        CURLOPT_ENCODING       => "",       // handle all encodings
	        // CURLOPT_USERAGENT      => "spider", // who am i
	        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
	        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
	        CURLOPT_TIMEOUT        => 120,      // timeout on response
	        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	        CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
	    );

	    $ch      = curl_init( $url );
	    curl_setopt_array( $ch, $options );
	    $content = curl_exec( $ch );
	    $err     = curl_errno( $ch );
	    $errmsg  = curl_error( $ch );
	    $header  = curl_getinfo( $ch );
	    curl_close( $ch );

	    $header['errno']   = $err;
	    $header['errmsg']  = $errmsg;
	    $header['content'] = $content;
	    return $header;
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

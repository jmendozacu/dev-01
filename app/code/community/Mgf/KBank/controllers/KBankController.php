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
 * @package    Mgf_KBank	
 * @author	   cherdchai Hinjumpa , bugcherd@gmail.com   089-003-5240
 * @website   http://magentothai.wordpress.com/
 */


/**
 * KBank Standard Checkout Controller
 *
 * @category   Mage
 * @package    Mgf_KBank
 * @author      Magento Core Team <core@magentocommerce.com>
 */
      
 
class Mgf_KBank_KBankController extends Mage_Core_Controller_Front_Action
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
     * Get singleton with KBank strandard order transaction information
     *
     * @return Mgf_KBank_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('KBank/standard');
    }

    /**
     * When a customer chooses KBank on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
		
		$session = Mage::getSingleton('checkout/session');
		$session->setKBankStandardQuoteId($session->getQuoteId());
		$order = Mage::getModel('sales/order');
		$order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
		$order->sendNewOrderEmail();
		$order->save();
		
		$this->getResponse()->setBody($this->getLayout()->createBlock('KBank/form_redirect')->toHtml());
		$session->unsQuoteId();

    }

    public function testAction(){
    	$order = Mage::getModel('sales/order');
		// $order->loadByIncrementId('1606080012');

		$message=Mage::helper('KBank')->__('Your payment is authorized by KBank ('. $KBankReturnData .').');
						
		if($order->getStatus() != Mage::getStoreConfig('payment/KBank/payment_success_status')){
			$order->setState(Mage::getStoreConfig('payment/KBank/payment_success_status'), true, $message);
			$order->save();
			//=> Create Invoice
			if (Mage::getStoreConfig('payment/KBank/payment_autoinvoice')=="1") {
				$invoice = Mage::getModel('sales/service_order',$order)->prepareInvoice();
                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                $invoice->register();
                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $transactionSave->save();
			}
			//=> End Create Invoice
		} else {
			$message .= 'Other -'.$order->getState().' : '.$message;
			$order->setState($order->getStatus(), true, $message);
			$order->save();
		}
    }
    /**
     * When a customer cancel payment from KBank.
     */
    public function cancelAction($OrderID, $APICall, $CancelResult, $ResponseMessage)
    {
		$ResponseMessge = "";
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getKBankStandardQuoteId(true));
		
		$order = Mage::getModel('sales/order');
		$order->loadByIncrementId($OrderID);
		
		// $payment = $order->getPayment();
		// $payment->setAdditionalInformation('approvecode',"Cancel");
		// $payment->setAdditionalInformation('paidnote',$CancelResult);
		// $payment->save();

		$state = Mage_Sales_Model_Order::STATE_CANCELED;
		$ResponseMessge = $order->getStatus() . " -> "  . $state;
		if($order->canCancel()) {
			$message=Mage::helper('KBank')->__("Cancelled ::: " .  $ResponseMessage);
			$order->cancel();
			$order->setState($state, true, $message);
			$order->save();
		}
		else {
			$ResponseMessge .= " (Order can not cancel)";
			$message=Mage::helper('KBank')->__("Order can not cancel :: ". $order->getStatus() ." (". $ResponseMessage .").");
			$order->addStatusToHistory($order->getStatus(), $message, false);	
			$order->save();		
		}
		
		
		
			Mage::getSingleton('checkout/session')->addError("Installment Payment has been cancelled and the transaction has been declined.");
			$this->_redirect('checkout/cart');		
		
    }

    /**
     * when KBank returns
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
        //	throw new Exception('Response doesn\'t contain GET /POST elements.', 20);
        }
        
		$Paidby = "KBank";
		// if(isset($response["paidsrc"])) $Paidby  =$response["paidsrc"];
		
		// echo "<p>paid by : " . $Paidby  ."</p>";
		//exit;
		
		
		switch ($Paidby) {
		    case "krungsri":
		        /************************ Krungsri***********************/
				$ReturnData = "";
				$PaidType = "";				
				$ReturnCode = "";
				$ReturnMessage = "";
				$ReturnMerchantID = "";
				$ReturnOrderNo = "";
				if(isset($response["rptsrc"])) $PaidType =$response["rptsrc"];
				if(isset($response["order_no"])) $ReturnOrderNo =$response["order_no"];
				if(isset($response["mid"])) $ReturnMerchantID=$response["mid"];
				if(isset($response["return"])) $ReturnCode =$response["return"];		
				if(isset($response["msg"])) $ReturnMessage= $response["msg"];
				
				
				//=> Data for show KBank return message
				$ReturnData = "Reply Back = ". $PaidType ."\n\r" .  
					"Response code = ". $ReturnCode ."\n\r" .  
					"Response message = ". $ReturnMessage ."\n\r" .  
					"Merchant ID = ". $ReturnMerchantID ."\n\r" .  
					"Merchant order no = ". $ReturnOrderNo ."\n\r";
				
				echo $ReturnOrderNo . "|" . $ReturnMerchantID .   "|" . $ReturnCode .  "|" . $ReturnMessage;
				
				$ResponseMessge = "";
				
				$order = Mage::getModel('sales/order');
				$order->loadByIncrementId($ReturnOrderNo);
				
				if (strtolower($order->getStatus()) == "pending") {
					//=> Pending
					if (($ReturnMessage=="success") && ($ReturnCode=="1")) {
						//=> Paid
						$state = Mage::getStoreConfig('payment/KBank/payment_success_status');
						$payment = $order->getPayment();
						$ResponseMessge = $order->getStatus() . " -> "  . $state;
					
						$message=Mage::helper("KBank")->__("Your payment is authorized by ". $Paidby ."  (". $ReturnData .").");
						$order->setState($state, true, $message);
						$order->save();
						
						//=> Save Payment Information
					    //$payment->setAdditionalInformation('approvecode',$KBankAuthorizeCode);
						//$payment->setAdditionalInformation('kresponsecode',$KBankResultCode);
						//$payment->setAdditionalInformation('kcardno',$KBankCardNo);
						//$payment->setAdditionalInformation('kcardtype',$KBankFILLSPACE);
						//$payment->setAdditionalInformation('kamount',$KBankAmount);
						//$payment->setAdditionalInformation('kbthamount',$KBankTHBAmount);
						//$payment->setAdditionalInformation('kcurrencycode',$KBankCURISO);
						//$payment->setAdditionalInformation('kcurrencyrate',$KBankFXRATE);
						//$payment->setAdditionalInformation('paidnote',$TranPaidNote);					
						//$payment->save();				
					
						$session = Mage::getSingleton('checkout/session');
						$session->setQuoteId($session->getKBankStandardQuoteId(true));
						/**
						* set the quote as inactive after back from KBank
						*/
						Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
						$this->_redirect('checkout/onepage/success', array('_secure'=>true));
					}
					else {
						//=> cancel
						$session = Mage::getSingleton('checkout/session');
						$session->setQuoteId($session->getKBankStandardQuoteId(true));
						/**
						* set the quote as inactive after back from KBank
						*/
						Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();				
						Mage::getSingleton('checkout/session')->addSuccess($Paidby . " Payment has been cancelled and the transaction has been declined.");
						
						//echo $MerchantInvoiceNo . ","  .  $BJCCallType . "," .  $TranPaidNote . ", " . $KBankReturnData;
						//exit;
						
						$this->cancelAction($ReturnOrderNo,"web", "" ,$ReturnData);				
						return false;						
						
						//=> cancel
					
					}
					//=> Pending - End
				}
				else {
					
					//=> Other Status
					$ResponseMessge = $order->getStatus() . " -> Add order history";
					$message=Mage::helper('KBank')->__("Your payment is authorized by ". $Paidby ." (". $ReturnData .").");
					$order->addStatusToHistory($order->getStatus(), $message, false);
					$order->save();

					//Mage::getSingleton('checkout/session')->addError($Paidby  . " Payment has been cancelled and the transaction has been declined.");
					$this->_redirect('checkout/cart');		
					//=> Other Status - End				
				}
				
				
				
		        break;
		   default:
		        //=> SmartPay by kbank
				//=> KBank Return Data
				//-> result = 00 = Approved, 01 = Other > Not Approved
				$KBankReturnData  = "";
				$KBankResultCode = ""; 
				$KBankAuthorizeCode = "";
				$KBankInvoice = "";
				$KBankCardNo = 0;
				
				$KBankAmount = 0;
				$KBankTHBAmount = 0;
				
				$KBankCURISO = "";
				$KBankFXRATE = "";
				$KBankFILLSPACE = "";
				
				$KBankMID = "";
				$KBankPLANID ="";
				$KBankPAYMONTH = "";
				$KBankINTTYPE= "";
				$KBankINTRATE = "";
				$KBankAMTPERMONTH = "";
				$KBankTOTALAMT = "";
				$KBankMANGFEE = "";
				$KBankINTMODE = "";
				
				$KBankMD5CHECKSUM = "";		
				$OnsiteMD5CHECKSUM = "";
				
				
				if(isset($response["HOSTRESP"])) $KBankResultCode =$response["HOSTRESP"];
				if(isset($response["AUTHCODE"])) $KBankAuthorizeCode=$response["AUTHCODE"];
				if(isset($response["RETURNINV"])) $KBankInvoice =$response["RETURNINV"];		
				if(isset($response["CARDNUMBER"])) $KBankCardNo= $response["CARDNUMBER"];
				
				if(isset($response["AMOUNT"])) $KBankAmount= $response["AMOUNT"];
				if(isset($response["THBAMOUNT"])) $KBankTHBAmount= $response["THBAMOUNT"];
				
				if(isset($response["CURISO"])) $KBankCURISO= $response["CURISO"];
				if(isset($response["FXRATE"])) $KBankFXRATE= $response["FXRATE"];
				if(isset($response["FILLSPACE"])) $KBankFILLSPACE= $response["FILLSPACE"];
				
				if(isset($response["MID"])) $KBankMID= $response["MID"];
				if(isset($response["PLANID"])) $KBankPLANID= $response["PLANID"];
				if(isset($response["PAYMONTH"])) $KBankPAYMONTH= $response["PAYMONTH"];
				if(isset($response["INTRATE"])) $KBankINTRATE= $response["INTRATE"];
				if(isset($response["AMTPERMONTH"])) $KBankAMTPERMONTH= $response["AMTPERMONTH"];
				
				if(isset($response["TOTALAMT"])) $KBankTOTALAMT= $response["TOTALAMT"];
				if(isset($response["MANGFEE"])) $KBankMANGFEE= $response["MANGFEE"];
				if(isset($response["INTMODE"])) $KBankINTMODE= $response["INTMODE"];
		
				if(isset($response["MD5CHECKSUM"])) $KBankMD5CHECKSUM= $response["MD5CHECKSUM"];
				
				$OnsiteMD5CHECKSUM = "";
				
				//=> Data for show KBank return message
				$KBankReturnData = "Response Code = ". $KBankResultCode ."\n\r" .  
						"Authorize Code = ". $KBankAuthorizeCode ."\n\r" . 
						"Invoice No = ". $KBankInvoice ."\n\r" . 
						"Credit Card Number = ". $KBankCardNo ."\n\r" . 
						"Transaction Amount = ". $KBankAmount ."\n\r" . 
						"Amount that merchant posts on Web site = ". $KBankTHBAmount ."\n\r" . 
						"Transaction currency = ". $KBankCURISO ."\n\r" . 
						"Exchange rate of transaction = ". $KBankFXRATE ."\n\r" . 
						"Type of Card = ". $KBankFILLSPACE ."\n\r";

				if (Mage::getStoreConfig('payment/KBank/smartpayactive')=="1") {
					$KBankReturnData .= "MID = ". $KBankMID ."\n\r" .  
						"Plan ID Value for Smartpay = ". $KBankPLANID ."\n\r" . 
						"Number of month for installment = ". $KBankPAYMONTH ."\n\r" . 
						"Interest calculation method for smart payment = ". $KBankINTTYPE ."\n\r" . 
						"Interest rate charged for smart payment per month = ". $KBankINTRATE ."\n\r" . 
						"Amount payment per Month for smart payment = ". $KBankAMTPERMONTH ."\n\r" . 
						"Total amount include Interest amount for smart payment = ". $KBankTOTALAMT ."\n\r" . 
						"Management Fee for smart payment = ". $KBankFXRATE ."\n\r" . 
						"Mode for Interest payment acceptance = ". $KBankINTMODE ."\n\r";		
				}
				
				$KBankReturnData .= "MD5CHECKSUM = ". $KBankMD5CHECKSUM ."\n\r" .  
										" Calulate MD5CHECKSUM = ". $KBankResultCode ."\n\r";
				
				//echo "<p>My Data : $KBankReturnData</p>";
					$MerchantInvoiceNo = substr($KBankInvoice,-10);
					if($KBankResultCode=="00")
					{
						
						$order = Mage::getModel('sales/order');
						$order->loadByIncrementId($MerchantInvoiceNo);
						$state = Mage::getStoreConfig('payment/KBank/payment_success_status');
						$message=Mage::helper('KBank')->__('Your payment is authorized by KBank ('. $KBankReturnData .').');
						
						if($order->getStatus() != Mage::getStoreConfig('payment/KBank/payment_success_status')){
							$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, "New : " .  $message, 1)->save(); 		
							$order->setStatus(Mage::getStoreConfig('payment/KBank/payment_success_status'), true, $message);
							$order->save();
							//=> Create Invoice
							if (Mage::getStoreConfig('payment/KBank/payment_autoinvoice')=="1") {
								$invoice = Mage::getModel('sales/service_order',$order)->prepareInvoice();
				                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
				                $invoice->register();
				                $transactionSave = Mage::getModel('core/resource_transaction')
				                    ->addObject($invoice)
				                    ->addObject($invoice->getOrder());
				                $transactionSave->save();
							}
							//=> End Create Invoice
						} else {
							$message .= 'Other -'.$order->getState().' : '.$message;
							$order->setState($order->getState(), true, $message);
							$order->setStatus(Mage::getStoreConfig('payment/KBank/payment_success_status'), true, $message);
							$order->save();
						}

						$session = Mage::getSingleton('checkout/session');
						$session->setQuoteId($session->getKBankStandardQuoteId(true));
						/**
						* set the quote as inactive after back from KBank
						*/
						Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
						$this->_redirect('checkout/onepage/success', array('_secure'=>true));
					}
					else
					{
						// $ReturnOrderNo,"web", "" ,$ReturnData
						$this->getCheckout()->setKBankErrorMessage('KBank UNSUCCESS ('. $KBankReturnData .')');   
						$this->cancelAction($MerchantInvoiceNo,'web','',$KBankReturnData);
						return false;
					}				
				
				
				
				//=> SmartPay by kbank end 
		        break;
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

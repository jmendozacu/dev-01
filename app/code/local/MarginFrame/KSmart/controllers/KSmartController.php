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
 * @package    Mgf_KSmart    
 * @author     cherdchai Hinjumpa , bugcherd@gmail.com   089-003-5240
 * @website   http://magentothai.wordpress.com/
 */


/**
 * KSmart Standard Checkout Controller
 *
 * @category   Mage
 * @package    Mgf_KSmart
 * @author      Magento Core Team <core@magentocommerce.com>
 */
      
 
class MarginFrame_KSmart_KSmartController extends Mage_Core_Controller_Front_Action
{
    
    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Get order
     *
     *  @return   Mage_Sales_Model_Order
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
     * Get singleton with KSmart strandard order transaction information
     *
     * @return Mgf_KSmart_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('KSmart/standard');
    }

    /**
     * When a customer chooses KSmart on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        
        $session = Mage::getSingleton('checkout/session');
        $session->setKSmartStandardQuoteId($session->getQuoteId());
        $order = Mage::getModel('sales/order');
        $order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
        // $order->sendNewOrderEmail();
        $order->save();
        
        $this->getResponse()->setBody($this->getLayout()->createBlock('KSmart/form_redirect')->toHtml());
        $session->unsQuoteId();

    }

    /**
     * When a customer cancel payment from KSmart.
     */
    public function cancelAction($OrderID, $APICall, $CancelResult, $ResponseMessage)
    {
        $ResponseMessge = "";
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getKSmartStandardQuoteId(true));
        
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($OrderID);
        
        $payment = $order->getPayment();
        $payment->setAdditionalInformation('approvecode',"Cancel");
        $payment->setAdditionalInformation('paidnote',$CancelResult);
        $payment->save();

        $state = Mage_Sales_Model_Order::STATE_CANCELED;
        $ResponseMessge = $order->getStatus() . " -> "  . $state;
        if($order->canCancel()) {
            $message=Mage::helper('KSmart')->__("Cancelled ::: " .  $ResponseMessage);
            $order->cancel();
            $order->setState($state, true, $message);
            $order->save();
        }
        else {
            $ResponseMessge .= " (Order can not cancel)";
            $message=Mage::helper('KSmart')->__("Order can not cancel :: ". $order->getStatus() ." (". $ResponseMessage .").");
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
     * when KSmart returns
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
        //  throw new Exception('Response doesn\'t contain GET /POST elements.', 20);
        }

        $Paidby = "";
        if(isset($response["paidsrc"])) $Paidby  =$response["paidsrc"];
        
        echo "<p>paid by : " . $Paidby  ."</p>";
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
                
                
                //=> Data for show KSmart return message
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
                        $state = Mage::getStoreConfig('payment/KSmart/payment_success_status');
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
                        $session->setQuoteId($session->getKSmartStandardQuoteId(true));
                        /**
                        * set the quote as inactive after back from KBank
                        */
                        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
                        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
                    }
                    else {
                        //=> cancel
                        $session = Mage::getSingleton('checkout/session');
                        $session->setQuoteId($session->getKSmartStandardQuoteId(true));
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
                //=> KSmart Return Data
                //-> result = 00 = Approved, 01 = Other > Not Approved
                $KSmartReturnData  = "";
                $KSmartResultCode = ""; 
                $KSmartAuthorizeCode = "";
                $KSmartInvoice = "";
                $KSmartCardNo = 0;
                
                $KSmartAmount = 0;
                $KSmartTHBAmount = 0;
                
                $KSmartCURISO = "";
                $KSmartFXRATE = "";
                $KSmartFILLSPACE = "";
                
                $KSmartMID = "";
                $KSmartPLANID ="";
                $KSmartPAYMONTH = "";
                $KSmartINTTYPE= "";
                $KSmartINTRATE = "";
                $KSmartAMTPERMONTH = "";
                $KSmartTOTALAMT = "";
                $KSmartMANGFEE = "";
                $KSmartINTMODE = "";
                
                $KSmartMD5CHECKSUM = "";     
                $OnsiteMD5CHECKSUM = "";
                
                
                if(isset($response["HOSTRESP"])) $KSmartResultCode =$response["HOSTRESP"];
                if(isset($response["AUTHCODE"])) $KSmartAuthorizeCode=$response["AUTHCODE"];
                if(isset($response["RETURNINV"])) $KSmartInvoice =$response["RETURNINV"];        
                if(isset($response["CARDNUMBER"])) $KSmartCardNo= $response["CARDNUMBER"];
                
                if(isset($response["AMOUNT"])) $KSmartAmount= $response["AMOUNT"];
                if(isset($response["THBAMOUNT"])) $KSmartTHBAmount= $response["THBAMOUNT"];
                
                if(isset($response["CURISO"])) $KSmartCURISO= $response["CURISO"];
                if(isset($response["FXRATE"])) $KSmartFXRATE= $response["FXRATE"];
                if(isset($response["FILLSPACE"])) $KSmartFILLSPACE= $response["FILLSPACE"];
                
                if(isset($response["MID"])) $KSmartMID= $response["MID"];
                if(isset($response["PLANID"])) $KSmartPLANID= $response["PLANID"];
                if(isset($response["PAYMONTH"])) $KSmartPAYMONTH= $response["PAYMONTH"];
                if(isset($response["INTRATE"])) $KSmartINTRATE= $response["INTRATE"];
                if(isset($response["AMTPERMONTH"])) $KSmartAMTPERMONTH= $response["AMTPERMONTH"];
                
                if(isset($response["TOTALAMT"])) $KSmartTOTALAMT= $response["TOTALAMT"];
                if(isset($response["MANGFEE"])) $KSmartMANGFEE= $response["MANGFEE"];
                if(isset($response["INTMODE"])) $KSmartINTMODE= $response["INTMODE"];
        
                if(isset($response["MD5CHECKSUM"])) $KSmartMD5CHECKSUM= $response["MD5CHECKSUM"];
                
                $OnsiteMD5CHECKSUM = "";
                
                //=> Data for show KSmart return message
                $KSmartReturnData = "Response Code = ". $KSmartResultCode ."\n\r" .  
                        "Authorize Code = ". $KSmartAuthorizeCode ."\n\r" . 
                        "Invoice No = ". $KSmartInvoice ."\n\r" . 
                        "Credit Card Number = ". $KSmartCardNo ."\n\r" . 
                        "Transaction Amount = ". $KSmartAmount ."\n\r" . 
                        "Amount that merchant posts on Web site = ". $KSmartTHBAmount ."\n\r" . 
                        "Transaction currency = ". $KSmartCURISO ."\n\r" . 
                        "Exchange rate of transaction = ". $KSmartFXRATE ."\n\r" . 
                        "Type of Card = ". $KSmartFILLSPACE ."\n\r";
                if (Mage::getStoreConfig('payment/KSmart/smartpayactive')=="1") {
                    $KSmartReturnData .= "MID = ". $KSmartMID ."\n\r" .  
                        "Plan ID Value for Smartpay = ". $KSmartPLANID ."\n\r" . 
                        "Number of month for installment = ". $KSmartPAYMONTH ."\n\r" . 
                        "Interest calculation method for smart payment = ". $KSmartINTTYPE ."\n\r" . 
                        "Interest rate charged for smart payment per month = ". $KSmartINTRATE ."\n\r" . 
                        "Amount payment per Month for smart payment = ". $KSmartAMTPERMONTH ."\n\r" . 
                        "Total amount include Interest amount for smart payment = ". $KSmartTOTALAMT ."\n\r" . 
                        "Management Fee for smart payment = ". $KSmartFXRATE ."\n\r" . 
                        "Mode for Interest payment acceptance = ". $KSmartINTMODE ."\n\r";       
                }
                
                $KSmartReturnData .= "MD5CHECKSUM = ". $KSmartMD5CHECKSUM ."\n\r" .  
                                        " Calulate MD5CHECKSUM = ". $KSmartResultCode ."\n\r";
        
                //echo "<p>My Data : $KSmartReturnData</p>";
        
                    if($KSmartResultCode=="00")
                    {
                        $MerchantInvoiceNo = substr($KSmartInvoice,-9);
                        
                        $order = Mage::getModel('sales/order');
                        $order->loadByIncrementId($MerchantInvoiceNo);
                        $state = Mage::getStoreConfig('payment/KSmart/payment_success_status');
                        $message=Mage::helper('KSmart')->__('Your payment is authorized by KSmart ('. $KSmartReturnData .').');
                        $order->setState($state, true, $message);
                        $order->save();
                        
                        //=> Create Invoice
                        if (Mage::getStoreConfig('payment/KSmart/payment_autoinvoice')=="1") {
                        
                            
                        
                        }
                        //=> End Create Invoice
                        
                        $session = Mage::getSingleton('checkout/session');
                        $session->setQuoteId($session->getKSmartStandardQuoteId(true));
                        /**
                        * set the quote as inactive after back from KSmart
                        */
                        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
                        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
                    }
                    else
                    {
                        $this->getCheckout()->setKSmartErrorMessage('KSmart UNSUCCESS ('. $KSmartReturnData .')');   
                        $this->cancelAction();
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

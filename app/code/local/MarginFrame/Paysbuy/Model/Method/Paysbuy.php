<?php
class MarginFrame_Paysbuy_Model_Method_Paysbuy extends Mage_Payment_Model_Method_Abstract
{
    protected $_formBlockType = 'Paysbuy/form_Paysbuy';
    protected $_infoBlockType = 'Paysbuy/info_Paysbuy';
    protected $_canSavePaysbuy     = false;
	protected $_code  = 'Paysbuy';
    
	/**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Autoeye_Paysbuy_Model_Info
     */
    public function assignData($data)
    {
		 

        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
		$info->setPaysbuyType($this->getPaysbuyAccountId1())	
			->setMerchant_Id($data->getMerchant_Id())
			->setTerminal_Id($data->getTerminal_Id())
			->setAmount($data->getAmount())
			->setRedirect_Url($data->getRedirect_Url())
			->setResponse_Url($data->getResponse_Url())
			->setIp_Address($data->getIp_Address())
			->setDetails($data->getDetails())
			->setOrder_Id($data->getOrder_Id())
			->setCurrencycode($data->getCurrencycode())
			->setResponse_Backgroundurl($data->getResponse_Backgroundurl())
			->setOptshowsummary($data->getOptshowsummary())
			->setOptcomcode($data->getOptcomcode());
        return $this;
    }

    /**
     * Prepare info instance for save
     *
     * @return Autoeye_Paysbuy_Model_Abstract
     */
    public function prepareSave()
    {
        $info = $this->getInfoInstance();
        if ($this->_canSavePaysbuy) {
            $info->setPaysbuyNumberEnc($info->encrypt($info->getPaysbuyNumber()));
        }
        $info->setPaysbuyNumber(null)
            ->setPaysbuyCid(null);
        return $this;
    }
	public function getProtocolVersion()
    {
        return '1.0';//$this->getConfigData('protocolversion');
    }
	
	/**
     * Get paypal session namespace
     *
     * @return Mage_Paysbuy_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('Paysbuy/session');
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
	/**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        
	    return $this->getCheckout()->getQuote();
    }
	
	public function getStandardCheckoutFormFields($option = '')
    {
        if ($this->getQuote()->getIsVirtual()) {
            $a = $this->getQuote()->getBillingAddress();
            $b = $this->getQuote()->getShippingAddress();
        } else {
            $a = $this->getQuote()->getShippingAddress();
            $b = $this->getQuote()->getBillingAddress();
        }
		 
		$showsummarybrforeredirect = (Mage::getStoreConfig('Paysbuy/Paysbuy/optshowsummary')=="1") ? "1" : "1";
		
		$data=$this->getQuoteData($option);
		$sArr = array(	
			'biz'                   => $data['Merchant_Id'],
			'psb'                       => $data['Terminal_Id'],
			'amt'                     => $data['Amount'],
			'postURL'                     => $data['Response_Url'],
			'itm'                     => $data['Details'],
			'inv'                 => $data['Order_Id'],
			'currencyCode' => $data['Currencycode'],
			'opt_fix_redirect' => $showsummarybrforeredirect,
			'reqURL' => $data['Response_Backgroundurl'],
			'optcomcode' => $data['optcomcode'],
			);
			
        $sReq = '';
        $rArr = array();
        foreach ($sArr as $k=>$v) {
            /*
            replacing & char with and. otherwise it will break the post
            */
            $value =  str_replace("&","and",$v);
            $rArr[$k] =  $value;
            $sReq .= '&'.$k.'='.$value;
        }
		 
        return $rArr;
    }

    public function getPaysbuyUrl()
    {
		 $url=$this->_getPaysbuyConfig()->getPaysbuyServerUrl();
         return $url;
    }
	
	
	 public function getOrderPlaceRedirectUrl()
    {
	         return Mage::getUrl('Paysbuy/Paysbuy/redirect');
    }
	public function getQuoteData($option = '')
    {					
	
		if ($option == 'redirect') {
    		$orderIncrementId = $this->getCheckout()->getLastRealOrderId();
    		$quote = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		} else {
			$quote = $this->getQuote();
		}

		$data=array();		 	
		if ($quote)
		{
			if ($quote->getIsVirtual()) {
				$a = $quote->getBillingAddress();
				$b = $quote->getShippingAddress();
			} else {
				$a = $quote->getShippingAddress();
				$b = $quote->getBillingAddress();
			}
				
			
			$grand_total =  $quote->getGrandTotal();
			//$grand_total = $grand_total*100;
			
			$data['Merchant_Id']                = Mage::getStoreConfig('payment/Paysbuy/merchantid');
			$data['Terminal_Id']                = Mage::getStoreConfig('payment/Paysbuy/terminalid');
			$data['Amount']                     = $grand_total;
			$data['Redirect_Url']           	= $this->_getPaysbuyConfig()->getPaysbuyRedirecturl();
			$data['Response_Url']           	= $this->_getPaysbuyConfig()->getPaysbuyResponseurl();
			$data['optcomcode']                 = Mage::getStoreConfig('payment/Paysbuy/optcomcode');
			$data['Ip_Address']                 = $_SERVER['REMOTE_ADDR'];
			
		$currency_code = $quote->getCurrencyCode();
		$cur = 764;
		switch($currency_code){
		case 'THB':
			$cur = 764;
			break;
		case 'AUD':
			$cur = 036;
			break;		
		case 'GBP':
			$cur = 826;
			break;	
		case 'EUR':
			$cur = 978;
			break;		
		case 'HKD':
			$cur = 344;
			break;		
		case 'JPY':
			$cur = 392;
			break;		
		case 'NZD':
			$cur = 554;
			break;
		case 'SGD':
			$cur = 702;
			break;	
		case 'CHF':
			$cur = 756;
			break;	
		case 'USD':
			$cur = 840;
			break;	
		default:
			$cur = 764;
		}	 			
			
			$data['Currencycode'] = $cur;
			$data['Response_Backgroundurl'] = $this->_getPaysbuyConfig()->getPaysbuyBackgroundurl();
			
			$items = $quote->getAllItems();
			foreach($items as $item) {
				$details = $item->getName();
			}
			
			$data['Details']                    = $details;
			$data['Order_Id']                   = $this->getCheckout()->getLastRealOrderId();
		}
		 
		return $data; 
	}
	
	protected function _getPaysbuyConfig()
    {
        return Mage::getSingleton('Paysbuy/config');
    }
}
?>
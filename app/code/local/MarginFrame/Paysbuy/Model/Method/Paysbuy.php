<?php
class MarginFrame_Paysbuy_Model_Method_Paysbuy extends Mage_Payment_Model_Method_Abstract
{
    protected $_formBlockType = 'Paysbuy/form_paysbuy';
    protected $_infoBlockType = 'Paysbuy/info_paysbuy';
    protected $_canSavePaysbuy     = false;
	protected $_code  = 'Paysbuy';
    
	/**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  MarginFrame_Paysbuy_Model_Info
     */
    public function assignData($data)
    {
		 

        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        Mage::helper('Paysbuy')->debug('Assign Again!!!');
        //Mage::helper('Paysbuy')->debug($data,true);
		// $info->setPaysbuyType($this->getPaysbuyAccountId1())	
		// 	->setMerchant_Id($data->getMerchant_Id())
		// 	->setTerminal_Id($data->getTerminal_Id())
		// 	->setAmount($data->getAmount())
		// 	->setRedirect_Url($data->getRedirect_Url())
		// 	->setResponse_Url($data->getResponse_Url())
		// 	->setIp_Address($data->getIp_Address())
		// 	->setDetails($data->getDetails())
		// 	->setOrder_Id($data->getOrder_Id())
		// 	->setCurrencycode($data->getCurrencycode())
		// 	->setResponse_Backgroundurl($data->getResponse_Backgroundurl())
		// 	->setOptshowsummary($data->getOptshowsummary())
		// 	->setOptcomcode($data->getOptcomcode());
        return $this;
    }

    public function getPaysbuyUrl($option='api')
    {
		
		$sandbox = Mage::getStoreConfig('payment/paysbuy/sandbox');
        if($sandbox){
        	if($option=='api'){
        		$url = 'https://demo.paysbuy.com/api_paynow/api_paynow.asmx?op=api_paynow_authentication_v3';
        	} else {
        		$url ='https://demo.paysbuy.com/paynow.aspx'.$option;
        	}
        } else {
            if($option=='api'){
        		$url = 'https://www.paysbuy.com/api_paynow/api_paynow.asmx?op=api_paynow_authentication_v3';
        	} else {
        		$url ='https://www.paysbuy.com/paynow.aspx'.$option;
        	}
        }
        return $url;
    }
	

    /**
     * Prepare info instance for save
     *
     * @return MarginFrame_Paysbuy_Model_Abstract
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
        return Mage::getSingleton('paysbuy/session');
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
    	$quote = $this->getQuote(); 
        if ($this->getQuote()->getIsVirtual()) {
            $a = $quote->getBillingAddress();
            $b = $quote->getShippingAddress();
        } else {
            $a = $quote->getShippingAddress();
            $b = $quote->getBillingAddress();
        }
		
		$storeConfig = Mage::getStoreConfig('payment/Paysbuy');
		
		$option ='';
		$data=$this->getQuoteData($option);
		
		$sArr = array(	
			'psbID'     => $storeConfig['psbID'],
			'username'  => $storeConfig['username'],
			'secureCode'=> $storeConfig['secureCode'],
			'inv'       => $data['Order_Id'],
			'itm'       => $data['Details'],
			'amt'       => $data['Amount'],
			'curr_type'=>	$storeConfig['currency'],
			'com'=>$storeConfig['com'],
			'method'=> ($storeConfig['method']==0 ? '2':$storeConfig['method']),
			'language'=>Mage::getStoreConfig('method/paysbuy/currency/'.$storeConfig['currency'].'/language'),
			'resp_front_url'=>$this->getPaysbuyResponseurl(),
			'resp_back_url'=>$this->getPaysbuyBackgroundurl(),
			'opt_fix_redirect'=>$storeConfig['opt_fix_redirect'],
			'opt_fix_method'=>($storeConfig['method']!=0 ? '1':'0'),// Fix use cannot change
			'opt_name'=>$data['customer_name'],
			'opt_email'=>$data['email'],
			'opt_address'=>$data['address'],
			'paypal_amt'=>'',
			'opt_detail'=>'',
			'opt_param'=> (empty($storeConfig['couponcode']) ? '':'cp_code='.$storeConfig['couponcode']),
			);
		Mage::helper('Paysbuy')->debug($storeConfig);
		Mage::helper('Paysbuy')->debug($sArr,true);
        return $sArr;
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
			
			$data['Amount']                     = $grand_total;
			$data['Ip_Address']                 = $quote->getData('remote_ip');
			$data['customer_name'] =	$quote->getData('customer_firstname').' '.$quote->getData('customer_middlename').' '.$quote->getData('customer_lastname');
			$data['email'] =	$quote->getData('customer_email');
			$data['address'] =	'';//$quote->getBillingAddress();
			$details = "";
			$items = $quote->getAllItems();
			foreach($items as $item) {
				$details = $item->getName();
			}
			
			$data['Details']                    = $details;
			$data['Order_Id']                   = $this->getCheckout()->getLastRealOrderId();

		}
		return $data; 
	}
	
	public function getPaysbuyRedirecturl()
	{
		  $url= Mage::getUrl('Paysbuy/Paysbuy/success',array('_secure' => true));
		 return $url;
	}
	public function getPaysbuyResponseurl()
	{  
		 $url= Mage::getUrl('Paysbuy/Paysbuy/success',array('_secure' => true));
		 return $url;
	}
	
	public function getPaysbuyBackgroundurl()
	{
		$url= Mage::getUrl('Paysbuy/Paysbuy/feed',array('_secure' => true));
		return $url;
	}	
}
?>
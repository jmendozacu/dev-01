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







class Mgf_KSmartpay_Model_Method_KSmartpay extends Mage_Payment_Model_Method_Abstract

{

    protected $_formBlockType = 'KSmartpay/form_KSmartpay';

    protected $_infoBlockType = 'KSmartpay/info_KSmartpay';

    protected $_canSaveKSmartpay     = false;

	protected $_code  = 'KSmartpay';

    

	/**

     * Assign data to info model instance

     *

     * @param   mixed $data

     * @return  Mgf_KSmartpay_Model_Info

     */

    public function assignData($data)

    {

        if (!($data instanceof Varien_Object)) {

            $data = new Varien_Object($data);

        }
        Mage::log($data,null,'addignData.txt',true);
        $info = $this->getInfoInstance();
		$_SESSION["InstallmentCode"] = $data->getcheck_no();

		$info->setKSmartpayType($this->getKSmartpayAccountId1())	
			->setMerchant_Id($data->getMerchant_Id())
			->setTerminal_Id($data->getTerminal_Id())
			->setAmount($data->getAmount())
			->setcheck_no($data->getcheck_no())
			->setRedirect_Url($data->getRedirect_Url())
			->setResponse_Url($data->getResponse_Url())
			->setIp_Address($data->getIp_Address())
			->setDetails($data->getDetails())
			->setOrder_Id($data->getOrder_Id()) 
			->setHashValue($data->getHashValue())
			;
		$info->setAdditionalInformation("code_selected",$data->getcheck_no());
        return $this;

    }



    /**

     * Prepare info instance for save

     *

     * @return Mgf_KSmartpay_Model_Abstract

     */

    public function prepareSave()

    {

        $info = $this->getInfoInstance();

        if ($this->_canSaveKSmartpay) {

            $info->setKSmartpayNumberEnc($info->encrypt($info->getKSmartpayNumber()));

        }

        $info->setKSmartpayNumber(null)

            ->setKSmartpayCid(null);

        return $this;

    }

	

	

    public function isAvailable($quote = null)
    {	
		$isActive =  (bool)(int)Mage::getStoreConfig('payment/KSmartpay/active');
		$CurrentAmount = (double)Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal();
		
		//=> Allowed IP Address
		if (trim(Mage::getStoreConfig('payment/KSmartpay/allowedip')) != "") {
			$ClientIP = long2ip(Mage::helper('core/http')->getRemoteAddr(true)); 
			if (trim(Mage::getStoreConfig('payment/KSmartpay/allowedip')) != $ClientIP) {
				$isActive = false;
			}
		}
		//return $isActive;
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
					$PrdInstallmentArray = explode(",", $PrdinstallmentsData); // 30 31 32 33
					foreach ($PrdInstallmentArray as $PrdInstallmentItem){
						//=> Check x - Start
    						//=> KBank A
    						if (Mage::getStoreConfigFlag('payment/KSmartpayA/active', $storeId)) {
    							
    							if (Mage::getStoreConfig('payment/KSmartpayA/filterlimit', $storeId)==$PrdInstallmentItem) {
									$PlanActive = true;
									if (trim(Mage::getStoreConfig('payment/KSmartpayA/min_order_total')) != "") {
										$PlanActive = $PlanActive && ($CurrentAmount >= (double)Mage::getStoreConfig('payment/KSmartpayA/min_order_total'));
									}
								
									if (trim(Mage::getStoreConfig('payment/KSmartpayA/max_order_total')) != "") {
										$PlanActive = $PlanActive && ($CurrentAmount <= (double)Mage::getStoreConfig('payment/KSmartpayA/max_order_total'));
									}
									if ($PlanActive) {
	    								// $xtext = Mage::getStoreConfig('payment/KSmartpayA/bjcmethod', $storeId);
	    								$xtext = "KSmartpayA";
    									$ProductInstallmentArray[$PrdInstallmentItem] = $xtext;
									}
    							}
    						}
    						//=> KBank B
    						if (Mage::getStoreConfigFlag('payment/KSmartpayB/active', $storeId)) {
    							if (Mage::getStoreConfig('payment/KSmartpayB/filterlimit', $storeId)==$PrdInstallmentItem) {
									$PlanActive = true;
									if (trim(Mage::getStoreConfig('payment/KSmartpayB/min_order_total')) != "") {
										$PlanActive = $PlanActive && ($CurrentAmount >= (double)Mage::getStoreConfig('payment/KSmartpayB/min_order_total'));
									}
						
									if (trim(Mage::getStoreConfig('payment/KSmartpayB/max_order_total')) != "") {
										$PlanActive = $PlanActive && ($CurrentAmount <= (double)Mage::getStoreConfig('payment/KSmartpayB/max_order_total'));
									}
									if ($PlanActive) {
    									// $xtext = Mage::getStoreConfig('payment/KSmartpayB/bjcmethod', $storeId);
    									$xtext = "KSmartpayB";
										$ProductInstallmentArray[$PrdInstallmentItem] = $xtext;
									}
    							}
    						}
    						//=> KBank C
    						if (Mage::getStoreConfigFlag('payment/KSmartpayC/active', $storeId)) {
    							if (Mage::getStoreConfig('payment/KSmartpayC/filterlimit', $storeId)==$PrdInstallmentItem) {
									$PlanActive = true;
									if (trim(Mage::getStoreConfig('payment/KSmartpayC/min_order_total')) != "") {
										$PlanActive = $PlanActive && ($CurrentAmount >= (double)Mage::getStoreConfig('payment/KSmartpayC/min_order_total'));
									}
						
									if (trim(Mage::getStoreConfig('payment/KSmartpayC/max_order_total')) != "") {
										$PlanActive = $PlanActive && ($CurrentAmount <= (double)Mage::getStoreConfig('payment/KSmartpayC/max_order_total'));
									}
									if ($PlanActive) {
    									// $xtext = Mage::getStoreConfig('payment/KSmartpayC/bjcmethod', $storeId);
    									$xtext = "KSmartpayC";
										$ProductInstallmentArray[$PrdInstallmentItem] = $xtext;
									}
    							}
    						}
    						
							//=> Krungsri A
    						if (Mage::getStoreConfigFlag('payment/KrungsriA/active', $storeId)) {
    							if (Mage::getStoreConfig('payment/KrungsriA/filterlimit', $storeId)==$PrdInstallmentItem) {
									$PlanActive = true;
									if (trim(Mage::getStoreConfig('payment/KrungsriA/min_order_total')) != "") {
										$PlanActive = $PlanActive && ($CurrentAmount >= (double)Mage::getStoreConfig('payment/KrungsriA/min_order_total'));
									}
						
									if (trim(Mage::getStoreConfig('payment/KrungsriA/max_order_total')) != "") {
										$PlanActive = $PlanActive && ($CurrentAmount <= (double)Mage::getStoreConfig('payment/KrungsriA/max_order_total'));
									}
									
									if ($PlanActive) {
    									// $xtext = Mage::getStoreConfig('payment/KrungsriA/bjcmethod', $storeId);
    									$xtext = "KrungsriA";
										$ProductInstallmentArray[$PrdInstallmentItem] = $xtext;
									}
    							}
    						}
							//=> Krungsri B
    						if (Mage::getStoreConfigFlag('payment/KrungsriB/active', $storeId)) {
    							if (Mage::getStoreConfig('payment/KrungsriB/filterlimit', $storeId)==$PrdInstallmentItem) {
									$PlanActive = true;
									if (trim(Mage::getStoreConfig('payment/KrungsriB/min_order_total')) != "") {
										$PlanActive = $PlanActive && ($CurrentAmount >= (double)Mage::getStoreConfig('payment/KrungsriB/min_order_total'));
									}
						
									if (trim(Mage::getStoreConfig('payment/KrungsriB/max_order_total')) != "") {
										$PlanActive = $PlanActive && ($CurrentAmount <= (double)Mage::getStoreConfig('payment/KrungsriB/max_order_total'));
									}
									
									if ($PlanActive) {
    									// $xtext = Mage::getStoreConfig('payment/KrungsriB/bjcmethod', $storeId);
    									$xtext = "KrungsriB";
										$ProductInstallmentArray[$PrdInstallmentItem] = $xtext;
									}
    							}
    						}
    						//=> Krungsri B
    						if (Mage::getStoreConfigFlag('payment/KrungsriC/active', $storeId)) {
    							if (Mage::getStoreConfig('payment/KrungsriC/filterlimit', $storeId)==$PrdInstallmentItem) {
									$PlanActive = true;
									if (trim(Mage::getStoreConfig('payment/KrungsriC/min_order_total')) != "") {
										$PlanActive = $PlanActive && ($CurrentAmount >= (double)Mage::getStoreConfig('payment/KrungsriC/min_order_total'));
									}
						
									if (trim(Mage::getStoreConfig('payment/KrungsriC/max_order_total')) != "") {
										$PlanActive = $PlanActive && ($CurrentAmount <= (double)Mage::getStoreConfig('payment/KrungsriC/max_order_total'));
									}
									
									if ($PlanActive) {
    									// $xtext = Mage::getStoreConfig('payment/KrungsriC/bjcmethod', $storeId);
    									$xtext = "KrungsriC";
										$ProductInstallmentArray[$PrdInstallmentItem] = $xtext;
									}
    							}
    						}
    						
							// //=> Ktc A
    			// 			if (Mage::getStoreConfigFlag('payment/KtcA/active', $storeId)) {
    			// 				if (Mage::getStoreConfig('payment/KtcA/filterlimit', $storeId)==$PrdInstallmentItem) {
							// 		$PlanActive = true;
							// 		if (trim(Mage::getStoreConfig('payment/KtcA/min_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount >= (double)Mage::getStoreConfig('payment/KtcA/min_order_total'));
							// 		}
						
							// 		if (trim(Mage::getStoreConfig('payment/KtcA/max_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount <= (double)Mage::getStoreConfig('payment/KtcA/max_order_total'));
							// 		}
									
							// 		if ($PlanActive) {
	    		// 						$xtext = Mage::getStoreConfig('payment/KtcA/bjcmethod', $storeId);
							// 			$ProductInstallmentArray[$PrdInstallmentItem] = $xtext;
							// 		}
    			// 				}
    			// 			}
    			// 			//=> Ktc B
    			// 			if (Mage::getStoreConfigFlag('payment/KtcB/active', $storeId)) {
    			// 				if (Mage::getStoreConfig('payment/KtcB/filterlimit', $storeId)==$PrdInstallmentItem) {
							// 		$PlanActive = true;
							// 		if (trim(Mage::getStoreConfig('payment/KtcB/min_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount >= (double)Mage::getStoreConfig('payment/KtcB/min_order_total'));
							// 		}
							// 		if (trim(Mage::getStoreConfig('payment/KtcB/max_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount <= (double)Mage::getStoreConfig('payment/KtcB/max_order_total'));
							// 		}
									
							// 		if ($PlanActive) {
	    		// 						$xtext = Mage::getStoreConfig('payment/KtcB/bjcmethod', $storeId);
							// 			$ProductInstallmentArray[$PrdInstallmentItem] = $xtext; 
							// 		}
    			// 				}
    			// 			}
							
							
							// //=> Hsbc A
    			// 			if (Mage::getStoreConfigFlag('payment/HsbcA/active', $storeId)) {
    			// 				if (Mage::getStoreConfig('payment/HsbcA/filterlimit', $storeId)==$PrdInstallmentItem) {
							// 		$PlanActive = true;
							// 		if (trim(Mage::getStoreConfig('payment/HsbcA/min_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount >= (double)Mage::getStoreConfig('payment/HsbcA/min_order_total'));
							// 		}
						
							// 		if (trim(Mage::getStoreConfig('payment/HsbcA/max_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount <= (double)Mage::getStoreConfig('payment/HsbcA/max_order_total'));
							// 		}
									
							// 		if ($PlanActive) {
	    		// 						$xtext = Mage::getStoreConfig('payment/HsbcA/bjcmethod', $storeId);
							// 			$ProductInstallmentArray[$PrdInstallmentItem] = $xtext;
							// 		}
    			// 				}
    			// 			}
    			// 			//=> Hsbc B
    			// 			if (Mage::getStoreConfigFlag('payment/HsbcB/active', $storeId)) {
    			// 				if (Mage::getStoreConfig('payment/HsbcB/filterlimit', $storeId)==$PrdInstallmentItem) {
							// 		$PlanActive = true;
							// 		if (trim(Mage::getStoreConfig('payment/HsbcB/min_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount >= (double)Mage::getStoreConfig('payment/HsbcB/min_order_total'));
							// 		}
							// 		if (trim(Mage::getStoreConfig('payment/HsbcB/max_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount <= (double)Mage::getStoreConfig('payment/HsbcB/max_order_total'));
							// 		}
									
							// 		if ($PlanActive) {
	    		// 						$xtext = Mage::getStoreConfig('payment/HsbcB/bjcmethod', $storeId);
							// 			$ProductInstallmentArray[$PrdInstallmentItem] = $xtext; 
							// 		}
    			// 				}
    			// 			}
							
							// //=> SCB A
    			// 			if (Mage::getStoreConfigFlag('payment/ScbA/active', $storeId)) {
    			// 				if (Mage::getStoreConfig('payment/ScbA/filterlimit', $storeId)==$PrdInstallmentItem) {
							// 		$PlanActive = true;
							// 		if (trim(Mage::getStoreConfig('payment/ScbA/min_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount >= (double)Mage::getStoreConfig('payment/ScbA/min_order_total'));
							// 		}
						
							// 		if (trim(Mage::getStoreConfig('payment/ScbA/max_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount <= (double)Mage::getStoreConfig('payment/ScbA/max_order_total'));
							// 		}
									
							// 		if ($PlanActive) {
	    		// 						$xtext = Mage::getStoreConfig('payment/ScbA/bjcmethod', $storeId);
							// 			$ProductInstallmentArray[$PrdInstallmentItem] = $xtext;
							// 		}
    			// 				}
    			// 			}
    			// 			//=> SCB B
    			// 			if (Mage::getStoreConfigFlag('payment/ScbB/active', $storeId)) {
    			// 				if (Mage::getStoreConfig('payment/ScbB/filterlimit', $storeId)==$PrdInstallmentItem) {
							// 		$PlanActive = true;
							// 		if (trim(Mage::getStoreConfig('payment/ScbB/min_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount >= (double)Mage::getStoreConfig('payment/ScbB/min_order_total'));
							// 		}
							// 		if (trim(Mage::getStoreConfig('payment/ScbB/max_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount <= (double)Mage::getStoreConfig('payment/ScbB/max_order_total'));
							// 		}
									
							// 		if ($PlanActive) {
	    		// 						$xtext = Mage::getStoreConfig('payment/ScbB/bjcmethod', $storeId);
							// 			$ProductInstallmentArray[$PrdInstallmentItem] = $xtext; 
							// 		}
    			// 				}
    			// 			}
							
							// //=> BBL A
    			// 			if (Mage::getStoreConfigFlag('payment/BblA/active', $storeId)) {
    			// 				if (Mage::getStoreConfig('payment/BblA/filterlimit', $storeId)==$PrdInstallmentItem) {
							// 		$PlanActive = true;
							// 		if (trim(Mage::getStoreConfig('payment/BblA/min_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount >= (double)Mage::getStoreConfig('payment/BblA/min_order_total'));
							// 		}
						
							// 		if (trim(Mage::getStoreConfig('payment/KtcA/max_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount <= (double)Mage::getStoreConfig('payment/BblA/max_order_total'));
							// 		}
									
							// 		if ($PlanActive) {
	    		// 						$xtext = Mage::getStoreConfig('payment/BblA/bjcmethod', $storeId);
							// 			$ProductInstallmentArray[$PrdInstallmentItem] = $xtext;
							// 		}
    			// 				}
    			// 			}
    			// 			//=> BBL B
    			// 			if (Mage::getStoreConfigFlag('payment/BblB/active', $storeId)) {
    			// 				if (Mage::getStoreConfig('payment/BblB/filterlimit', $storeId)==$PrdInstallmentItem) {
							// 		$PlanActive = true;
							// 		if (trim(Mage::getStoreConfig('payment/BblB/min_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount >= (double)Mage::getStoreConfig('payment/BblB/min_order_total'));
							// 		}
							// 		if (trim(Mage::getStoreConfig('payment/BblB/max_order_total')) != "") {
							// 			$PlanActive = $PlanActive && ($CurrentAmount <= (double)Mage::getStoreConfig('payment/BblB/max_order_total'));
							// 		}
									
							// 		if ($PlanActive) {
	    		// 						$xtext = Mage::getStoreConfig('payment/BblB/bjcmethod', $storeId);
							// 			$ProductInstallmentArray[$PrdInstallmentItem] = $xtext; 
							// 		}
    			// 				}
    			// 			}
							
						//=> Check x - End
						
						
					}
					if ($LoopNo==1) {
						$AvaliableArray = array_merge($AvaliableArray,$ProductInstallmentArray);
					}
					else {
						$AvaliableArray = array_intersect($AvaliableArray,$ProductInstallmentArray);
					}
					
					if (count($AvaliableArray) < 1) {
						$cartcondition = false;
					}
					//=> Loop Installment array
					$LoopNo++;
				}
				else {
					$cartcondition = false;
				}
				//=> End Product  - installments_attribute
			}
			// => (Price > 0) and (is Installment)
		}
		//=> Loop cart items
		
		if ($cartcondition) {
			if (count($AvaliableArray) < 1) {
				$cartcondition = false;
			}
		} else {
			if (trim(Mage::getStoreConfig('payment/KSmartpay/min_amount')) != "") {
				if($CurrentAmount > (double)Mage::getStoreConfig('payment/KSmartpay/min_amount')){
					$cartcondition = true;
				}
			}
		}
		if (!$cartcondition) {
			unset($AvaliableArray);
		}
		//echo "<p>Installments Plan</p>";
		//print_r($AvaliableArray);
		//=> Filter Limited = End
		return $isActive && $cartcondition;
		//return true;
    }			

		

	

	

	public function getProtocolVersion()

    {

        return '1.0';//$this->getConfigData('protocolversion');

    }

	

	/**

     * Get paypal session namespace

     *

     * @return Mage_KSmartpay_Model_Session

     */

    public function getSession()

    {

        return Mage::getSingleton('KSmartpay/session');

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

		 

		$data=$this->getQuoteData($option);

		

		//=> MD5 Secure Hash
		
		//$_SESSION["InstallmentCode"] = $data->getcheck_no();
		//echo "<p>aaa installment select : " . $_SESSION["InstallmentCode"] . "</p>";
		$PaidbyCard = ""; $PaidTerm = "";
		if ($_SESSION["InstallmentCode"]  != "") {
			$PaymentArray = explode("|",trim($_SESSION["InstallmentCode"]));
			if (count($PaymentArray)==2) {
				$PaidbyCard = $PaymentArray[0];
				$PaidTerm = $PaymentArray[1];
			}
		}
		
		//echo "<p>aa zzzz installment select : " . $PaidbyCard . "</p>";
		//echo "<p>aa zzzz installment select : " . $PaidTerm  . "</p>";
		
		$ShopID = (count($PaymentArray) !=2) ? "" : trim(Mage::getStoreConfig("payment/". trim($PaidbyCard)  ."/shopid"));
		$Payterm2 = $PaidTerm;

		$sArr = array(	
			'MERCHANT2'                   => trim(Mage::getStoreConfig("payment/". trim($PaidbyCard)  ."/smpmerchantid")),
			'TERM2'                       => trim(Mage::getStoreConfig("payment/". trim($PaidbyCard)  ."/smpterminalid")),
			'AMOUNT2'                     => $data['Amount'],
			'URL2'                     => $data['Response_Url'],
			'RESPURL'                     => $data['Response_Url'],
			'IPCUST2'                     => $data['Ip_Address'],
			'DETAIL2'                     => $data['Details'],
			'INVMERCHANT'                 => $data['Order_Id'],
			'FILLSPACE'                 => 'Y',
			'SHOPID' => $ShopID,
			'PAYTERM2' => $Payterm2,
			'txtCHECKSUM'   => Mage::getStoreConfig('payment/KSmartpay/md5code'),
			'customer_name'                     => $data['CustomerName'],
			'customer_email'                     => $data['CustomerEmail'],
			'customer_phone'                 => $data['CustomerTel'],
			'installment_card'                 => $PaidbyCard,
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



    public function getKSmartpayUrl()

    {

		 $url=$this->_getKSmartpayConfig()->getKSmartpayServerUrl();

         return $url;

    }

	

	

	 public function getOrderPlaceRedirectUrl()

    {

	         return Mage::getUrl('KSmartpay/KSmartpay/redirect');

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

			

			//$InvoiceNo = str_pad($data['INVOICENO'],20, "0", STR_PAD_LEFT);

			$AmountText = str_pad(number_format($grand_total,2, '', ''),12, "0", STR_PAD_LEFT);

			

			//echo "<p>Smart Pay : " . Mage::getStoreConfig('payment/KSmartpay/smartpayactive')  ."</p>"; 
			
			//echo "<p>xxx installment select : " . $_SESSION["InstallmentCode"] . "</p>";
			$PaidbyCard = ""; $PaidTerm = ""; $ShopID = "";  $Payterm2 = "";
			if (isset($_SESSION["InstallmentCode"])) {			
				if ($_SESSION["InstallmentCode"] !="") {
					$PaymentArray = explode("|",trim($_SESSION["InstallmentCode"]));
					if (count($PaymentArray)==2) {
						$PaidbyCard = $PaymentArray[0];
						$PaidTerm = $PaymentArray[1];
					}
				}
			}
			
			$Payterm2 = $PaidTerm;
			$ShopID = trim(Mage::getStoreConfig("payment/". trim($PaidbyCard)  ."/shopid"));
			$data['Merchant_Id'] = ($PaidbyCard=="") ? "" :  trim(Mage::getStoreConfig("payment/". trim($PaidbyCard)  ."/smpmerchantid"));
			$data['Terminal_Id'] = ($PaidbyCard=="") ? "" :  trim(Mage::getStoreConfig("payment/". trim($PaidbyCard)  ."/smpterminalid"));


			$data['Amount']                     =  $AmountText;

			$data['Redirect_Url']           	= $this->_getKSmartpayConfig()->getKSmartpayRedirecturl();

			$data['Response_Url']           	= $this->_getKSmartpayConfig()->getKSmartpayRedirecturl();

			//=> $this->_getKSmartpayConfig()->getKSmartpayResponseurl();

			$data['Ip_Address']                 = ($_SERVER['SERVER_ADDR']=="::1") ? "110.171.35.14" : $_SERVER['SERVER_ADDR'];
			
			

			$details = "";

			$items = $quote->getAllItems();

			foreach($items as $item) {

				if ($details=="") {

					$details .= $item->getName();

				}

				else {

					$details .= ", " . $item->getName();

				}

			}

			if (strlen($details) > 250) {

				$details = substr($details,0,250);

			}

			

			$data['Details'] = $details;

			

			//=> Start Add New

			

			

			

			//=> End Add New

			$BillingInfo = $quote->getBillingAddress();

			

			$data['CustomerEmail'] = $BillingInfo->getEmail();

			$data['CustomerName'] = $BillingInfo->getPrefix();

			if (trim($BillingInfo->getFirstname()) != "") {

				$data['CustomerName'] .= ($data['CustomerName']!="") ? " " : "";

				$data['CustomerName'] .= trim($BillingInfo->getFirstname());

			}



			if (trim($BillingInfo->getMiddlename()) != "") {

				$data['CustomerName'] .= ($data['CustomerName']!="") ? " " : "";

				$data['CustomerName'] .= trim($BillingInfo->getMiddlename());

			}



			if (trim($BillingInfo->getLastname()) != "") {

				$data['CustomerName'] .= ($data['CustomerName']!="") ? " " : "";

				$data['CustomerName'] .= trim($BillingInfo->getLastname());

			}



			if (trim($BillingInfo->getSuffix()) != "") {

				$data['CustomerName'] .= ($data['CustomerName']!="") ? " " : "";

				$data['CustomerName'] .= trim($BillingInfo->getSuffix());

			}

			

			$data['CustomerTel'] = $BillingInfo->getTelephone();		

			

			

			



			$data['InstallmentCard'] = $PaidbyCard;	

			$InvoiceNo = $this->getCheckout()->getLastRealOrderId();

			$data['Order_Id'] = $InvoiceNo;
			
			$BJCPaymentUrl = Mage::getStoreConfig('payment/Bjcpayment/bjcpaymenturl');

			

			//$data['Redirect_Url']   = "http://www.voltztore.com/KBank/KBank/success";

			//$data['Response_Url']  = "http://www.voltztore.com/KBank/KBank/success";

			

			$data['Redirect_Url']   = $BJCPaymentUrl . "/" .  str_replace("{orderno}", trim($InvoiceNo),$this->_getKSmartpayConfig()->getKSmartpayRedirecturl());

			$data['Response_Url'] = $BJCPaymentUrl . "/" .  str_replace("{orderno}", trim($InvoiceNo),$this->_getKSmartpayConfig()->getKSmartpayResponseurl());			

									

			//=> Old

			//$InvoiceNo = $this->getCheckout()->getLastRealOrderId();

			//$data['Order_Id'] = $InvoiceNo;

			



		}

		 

		return $data; 

	}

	

	protected function _getKSmartpayConfig()

    {

        return Mage::getSingleton('KSmartpay/config');

    }

}

 
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







class Mgf_KBank_Model_Method_KBank extends Mage_Payment_Model_Method_Abstract

{

    protected $_formBlockType = 'KBank/form_KBank';

    protected $_infoBlockType = 'KBank/info_KBank';

    protected $_canSaveKBank     = false;

	protected $_code  = 'KBank';

    

	/**

     * Assign data to info model instance

     *

     * @param   mixed $data

     * @return  Mgf_KBank_Model_Info

     */

    public function assignData($data)

    {

        if (!($data instanceof Varien_Object)) {

            $data = new Varien_Object($data);

        }
        $info = $this->getInfoInstance();

		// $info->setKBankType($this->getKBankAccountId1())	
		// 	->setMerchant_Id($data->getMerchant_Id())
		// 	->setTerminal_Id($data->getTerminal_Id())
		// 	->setAmount($data->getAmount())
		// 	->setcheck_no($data->getcheck_no())
		// 	->setRedirect_Url($data->getRedirect_Url())
		// 	->setResponse_Url($data->getResponse_Url())
		// 	->setIp_Address($data->getIp_Address())
		// 	->setDetails($data->getDetails())
		// 	->setOrder_Id($data->getOrder_Id()) 
		// 	->setHashValue($data->getHashValue())
		// 	;
			
        return $this;

    }



    /**

     * Prepare info instance for save

     *

     * @return Mgf_KBank_Model_Abstract

     */

    public function prepareSave()

    {

        $info = $this->getInfoInstance();

        if ($this->_canSaveKBank) {

            $info->setKBankNumberEnc($info->encrypt($info->getKBankNumber()));

        }

        $info->setKBankNumber(null)

            ->setKBankCid(null);

        return $this;

    }

	

	

    public function isAvailable($quote = null)
    {	
		$isActive =  (bool)(int)Mage::getStoreConfig('payment/KBank/active');
		$CurrentAmount = (double)Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal();
		
		//=> Allowed IP Address
		if (trim(Mage::getStoreConfig('payment/KBank/allowedip')) != "") {
			$ClientIP = long2ip(Mage::helper('core/http')->getRemoteAddr(true)); 
			if (trim(Mage::getStoreConfig('payment/KBank/allowedip')) != $ClientIP) {
				$isActive = false;
			}
		}
		return $isActive;
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


		$sArr = array(	
			'MERCHANT2'                   => trim(Mage::getStoreConfig("payment/KBank/smpmerchantid")),
			'TERM2'                       => trim(Mage::getStoreConfig("payment/KBank/smpterminalid")),
			'AMOUNT2'                     => $data['Amount'],
			'URL2'                     => $data['Response_Url'],
			'RESPURL'                     => $data['Response_Url'],
			'IPCUST2'                     => $data['Ip_Address'],
			'DETAIL2'                     => $data['Details'],
			'INVMERCHANT'                 => $data['Order_Id'],
			'FILLSPACE'                 => 'Y',
			'SHOPID' => trim(Mage::getStoreConfig("payment/KBank/shopid")),
			'PAYTERM2' => "",
			'txtCHECKSUM'   => Mage::getStoreConfig('payment/KBank/md5code'),
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



    public function getKBankUrl()

    {

		 $url=$this->_getKBankConfig()->getKBankServerUrl();

         return $url;

    }

	

	

	 public function getOrderPlaceRedirectUrl()

    {

	         return Mage::getUrl('KBank/KBank/redirect');

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

			$AmountText = str_pad(number_format($grand_total,2, '', ''),12, "0", STR_PAD_LEFT);

			$data['Amount']                     =  $AmountText;
			$data['Redirect_Url']           	= $this->_getKBankConfig()->getKBankRedirecturl();
			$data['Response_Url']           	= $this->_getKBankConfig()->getKBankRedirecturl();
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

			$InvoiceNo = $this->getCheckout()->getLastRealOrderId();

			$data['Order_Id'] = $InvoiceNo;

		}

		 

		return $data; 

	}

	

	protected function _getKBankConfig()

    {

        return Mage::getSingleton('KBank/config');

    }

}

 
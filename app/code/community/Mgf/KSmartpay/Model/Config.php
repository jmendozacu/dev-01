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
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * KSmartpay configuration model
 *
 * Used for retrieving configuration data by KSmartpay models
 *
 * @category   Mage
 * @package    Mgf_KSmartpay
 * @author	   cherdchai Hinjumpa , bugcherd@gmail.com   089-003-5240
 * @website   http://magentothai.wordpress.com/
 */
class Mgf_KSmartpay_Model_Config
{
    protected static $_methods;

    /**
     * Retrieve active system KSmartpay
     *
     * @param   mixed $store
     * @return  array
     */
    public function getActiveMethods($store=null)
    {
        $methods = array();
        $config = Mage::getStoreConfig('KSmartpay', $store);
        foreach ($config as $code => $methodConfig) {
            if (Mage::getStoreConfigFlag('KSmartpay/'.$code.'/active', $store)) {
                $methods[$code] = $this->_getMethod($code, $methodConfig);
            }
        }
        return $methods;
    }

    /**
     * Retrieve all system KSmartpay
     *
     * @param mixed $store
     * @return array
     */
    public function getAllMethods($store=null)
    {
        $methods = array();
        $config = Mage::getStoreConfig('KSmartpay', $store);
        foreach ($config as $code => $methodConfig) {
            $methods[$code] = $this->_getMethod($code, $methodConfig);
        }
        return $methods;
    }

    protected function _getMethod($code, $config, $store=null)
    {
        if (isset(self::$_methods[$code])) {
            return self::$_methods[$code];
        }
        $modelName = $config['model'];
        $method = Mage::getModel($modelName);
        $method->setId($code)->setStore($store);
        self::$_methods[$code] = $method;
        return self::$_methods[$code];
    }

    /**
     * Retrieve array of credit card types
     *
     * @return array
     */
	 /*
    public function getKSmartpayServiceTypes()
    {
        $resource = Mage::getSingleton('core/resource');
			$read = $resource->getConnection('catalog_read');
			$paymentKSmartpayServicesTable = (string)Mage::getConfig()->getTablePrefix() . 'payment_KSmartpay_module_services';
			$select = $read->select()
				->from(array('pems'=>$paymentKSmartpayServicesTable)) ;
				
		$res = $read->fetchAll($select);
		$types = array();
		foreach ($res as $data) {
            $types[$data['service_type']] = $data['financial_product_name'];
        }
		
        return $types;
    }*/
	 
    /**
     * Retrieve list of months translation
     *
     * @return array
     */
    public function getMonths()
    {
        $data = Mage::app()->getLocale()->getTranslationList('month');
        foreach ($data as $key => $value) {
            $monthNum = ($key < 10) ? '0'.$key : $key;
            $data[$key] = $monthNum . ' - ' . $value;
        }
        return $data;
    }

    /**
     * Retrieve array of available years
     *
     * @return array
     */
    public function getYears()
    {
        $years = array();
        $first = date("Y");

        for ($index=0; $index <= 10; $index++) {
            $year = $first + $index;
            $years[$year] = $year;
        }
        return $years;
    }

    /**
     * Statis Method for compare sort order of CC Types
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    static function compareKSmartpayTypes($a, $b)
    {
        if (!isset($a['order'])) {
            $a['order'] = 0;
        }

        if (!isset($b['order'])) {
            $b['order'] = 0;
        }

        if ($a['order'] == $b['order']) {
            return 0;
        } else if ($a['order'] > $b['order']) {
            return 1;
        } else {
            return -1;
        }

    }
	public function getKSmartpayServerUrl()
	{
	     //$url='https://www.KSmartpay.com/paynow.aspx"';
		 //$url='http://demo.KSmartpay.com/paynow.aspx"';
		 $url = Mage::getStoreConfig('payment/KSmartpay/paymentgatewayurl');
         return $url;
	}
	
	public function getKSmartpayRedirecturl()
	{
		 // $url=Mage::getBaseUrl().'checkout/cart/';
		 $url= Mage::getUrl('KSmartpay/KSmartpay/success',array('_secure' => true));
		 // $url=  "kbankprocess.php";
		 return $url;
	}
	public function getKSmartpayResponseurl()
	{
		 // $url=Mage::getBaseUrl().'checkout/cart/';
		 // $url= "kbankcallapi.php";
		 $url= Mage::getUrl('KSmartpay/KSmartpay/success',array('_secure' => true));
		 return $url;
	}
	
    /**
     * Returns the custom text for this payment method
     *
     * @return string Custom text
     */
    public function getCustomText()
    {
        return Mage::getStoreConfig('payment/KSmartpay/checkouttext');
    }	

}
		
 
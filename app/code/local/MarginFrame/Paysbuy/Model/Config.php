<?php
class MarginFrame_Paysbuy_Model_Config
{
    protected static $_methods;

    /**
     * Retrieve active system Paysbuy
     *
     * @param   mixed $store
     * @return  array
     */
    public function getActiveMethods($store=null)
    {
        $methods = array();
        $config = Mage::getStoreConfig('Paysbuy', $store);
        foreach ($config as $code => $methodConfig) {
            if (Mage::getStoreConfigFlag('Paysbuy/'.$code.'/active', $store)) {
                $methods[$code] = $this->_getMethod($code, $methodConfig);
            }
        }
        return $methods;
    }

    /**
     * Retrieve all system Paysbuy
     *
     * @param mixed $store
     * @return array
     */
    public function getAllMethods($store=null)
    {
        $methods = array();
        $config = Mage::getStoreConfig('Paysbuy', $store);
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
    public function getPaysbuyServiceTypes()
    {
        $resource = Mage::getSingleton('core/resource');
			$read = $resource->getConnection('catalog_read');
			$paymentPaysbuyServicesTable = (string)Mage::getConfig()->getTablePrefix() . 'payment_Paysbuy_module_services';
			$select = $read->select()
				->from(array('pems'=>$paymentPaysbuyServicesTable)) ;
				
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
    static function comparePaysbuyTypes($a, $b)
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
	public function getPaysbuyServerUrl()
	{
	     //$url='https://www.paysbuy.com/paynow.aspx"';
		 //$url='http://demo.paysbuy.com/paynow.aspx"';
		 $url = Mage::getStoreConfig('payment/Paysbuy/paymentgatewayurl');
         return $url;
	}
	
	public function getPaysbuyRedirecturl()
	{
		 // $url=Mage::getBaseUrl().'checkout/cart/';
		  $url= Mage::getUrl('Paysbuy/Paysbuy/success',array('_secure' => true));
		 return $url;
	}
	public function getPaysbuyResponseurl()
	{
		 // $url=Mage::getBaseUrl().'checkout/cart/';
		 $url= Mage::getUrl('Paysbuy/Paysbuy/success',array('_secure' => true));
		 return $url;
	}
	
	public function getPaysbuyBackgroundurl()
	{
		 // $url=Mage::getBaseUrl().'checkout/cart/';
		 $url= Mage::getUrl('Paysbuy/Paysbuy/feed',array('_secure' => true));
		 return $url;
	}	

}
?>
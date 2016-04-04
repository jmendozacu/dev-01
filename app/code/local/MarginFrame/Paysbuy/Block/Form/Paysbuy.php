<?php
class MarginFrame_Paysbuy_Block_Form_Paysbuy extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
                $this->setTemplate('Paysbuy/form/Paysbuy.phtml');
    }

    /**
     * Retrieve direcpay configuration object
     *
     * @return MarginFrame_Paysbuy_Model_Config
     */
    protected function _getPaysbuyConfig()
    {
        return Mage::getSingleton('Paysbuy/config');
    }
    

   
    /**
     * Retrieve availables credit card types
     *
     * @return array
     */
    public function getPaysbuyServiceTypes()
    {
         
        
        // $types = $this->_getPaysbuyConfig()->getPaysbuyServiceTypes();
        // if ($method = $this->getMethod()) {
        //     $availableTypes = $method->getConfigData('Paysbuytypes');
        //     if ($availableTypes) {
        //         $availableTypes = explode(',', $availableTypes);
        //         foreach ($types as $code=>$name) {
        //             if (!in_array($code, $availableTypes)) {
        //                 unset($types[$code]);
        //             }
        //         }
        //     }
        // }
        
        return $types;
    }
    
    /**
     * Retrieve credit card expire months
     *
     * @return array
     */
    public function getPaysbuyMonths()
    {
        // $months = $this->getData('Paysbuy_months');
        // if (is_null($months)) {
        //     $months[0] =  $this->__('Month');
        //     $months = array_merge($months, $this->_getPaysbuyConfig()->getMonths());
        //     $this->setData('Paysbuy_months', $months);
        // }
        // return $months;
    }

    /**
     * Retrieve credit card expire years
     *
     * @return array
     */
    public function getPaysbuyYears()
    {
        // $years = $this->getData('Paysbuy_years');
        // if (is_null($years)) {
        //     $years = $this->_getPaysbuyConfig()->getYears();
        //     $years = array(0=>$this->__('Year'))+$years;
        //     $this->setData('Paysbuy_years', $years);
        // }
        // return $years;
    }

    /**
     * Retrive has verification configuration
     *
     * @return boolean
     */
    public function hasVerification()
    {
        if ($this->getMethod()) {
            $configData = $this->getMethod()->getConfigData('useccv');
            if(is_null($configData)){
                return true;
            }
            return (bool) $configData;
        }
        return true;
    }
    public function getQuoteData()
    {
        return $this->getMethod()->getQuoteData();
    }
    public function getBillingAddress()
    {
        if ($this->getMethod())
        {
            $this->getMethod()->getQuote();
            $aa= $this->getMethod()->getQuote()->getBillingAddress()->getCountry();
        }
    }
}

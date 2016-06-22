<?php
class MarginFrame_Paysbuy_Model_Adminhtml_System_Config_Source_Currency
{
    public function toOptionArray()
    {
        $options =  array();


        $methods = Mage::getStoreConfig('method/Paysbuy/currency');
        foreach ($methods as $code => $method) {
            $options[] = array(
                'value' => $method['value'],
                'label' => $method['title']
            );
        }
        return $options;
    }
} 

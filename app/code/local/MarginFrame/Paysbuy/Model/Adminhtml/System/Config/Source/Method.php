<?php
class MarginFrame_Paysbuy_Model_Adminhtml_System_Config_Source_Method
{
    public function toOptionArray()
    {
        $options =  array();

        $options[] = array(
            'value'=>'0',
            'label'=>'---- Use all method ----'
        );
        $methods = Mage::getStoreConfig('method/paysbuy/methodall');
        foreach ($methods as $code => $method) {
            $options[] = array(
                'value' => $method['value'],
                'label' => $method['title']
            );
        }
        return $options;
    }
} 

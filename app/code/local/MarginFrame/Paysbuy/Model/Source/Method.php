<?php

class MarginFrame_Paysbuy_Model_Source_Method
{
    public function toOptionArray()
    {
        $options =  array();

        $methods = Mage::getSingleton('payment/Paysbuy/method');
        foreach ($methods as $code => $method) {
                $options[] = array(
                    'value' => $method['value'],
                    'label' => $method['title']
                );
            }
        }
        return $options;
    }
}

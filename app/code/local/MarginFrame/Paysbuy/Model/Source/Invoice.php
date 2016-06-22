<?php
class MarginFrame_Paysbuy_Model_Source_Invoice
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => MarginFrame_Paysbuy_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => Mage::helper('core')->__('Yes')
            ),
            array(
                'value' => '',
                'label' => Mage::helper('core')->__('No')
            ),
        );
    }
}
?>
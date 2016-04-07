<?php
class Magebuzz_RegisterSource_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{
    protected function _prepareColumns(){
        $this->addColumnAfter('customer', array(
            'header'    =>  Mage::helper('customer')->__('Source'),
            'width'     =>  '100',
            'index'     =>  'customer_source'
        ),'email');
        return parent::_prepareColumns();
    }
}
			
<?php
class Magebuzz_RegisterSource_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->addAttributeToSelect('customer_source')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns(){
        $this->addColumnAfter('customer', array(
            'header'    =>  Mage::helper('customer')->__('Source'),
            'width'     =>  '100',
            'type'      => 'options',
            'options'   =>  array('Website','Facebook'),
            'index'     =>  'customer_source'
        ),'email');
        return parent::_prepareColumns();
    }
}
			
<?php
class Magpleasure_Blog_Model_Categorystyle extends Varien_Object{
    const HOME_DECOR       = 1;
    const PROMOTION      = 2;
    const NEWEVENT_CSR   = 3;
    const INDEX_PROJECT   = 4;

    static public function getOptionArray()
    {
        return array(
            self::HOME_DECOR       => Mage::helper('mpblog')->__('Home decor'),
            self::PROMOTION      => Mage::helper('mpblog')->__('Promotions'),
            self::NEWEVENT_CSR   => Mage::helper('mpblog')->__('New & Event, CRS'),
            self::INDEX_PROJECT   => Mage::helper('mpblog')->__('Index Project')
        );
    }
}
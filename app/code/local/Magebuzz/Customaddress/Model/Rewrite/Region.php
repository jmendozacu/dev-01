<?php
/*
* Copyright (c) 2015 www.magebuzz.com 
*/
class Magebuzz_Customaddress_Model_Rewrite_Region extends Mage_Directory_Model_Region {

	public function getName()
    {
        $field_name = Mage::helper('customaddress')->getTextColumnName();
        $name = $this->getData($field_name);
        return $name;
    }
    
}
<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Dealerlocator_Block_Adminhtml_Product extends Mage_Adminhtml_Block_Widget_Form_Container {
    public function __construct() {
        die('333');
        parent::__construct();
        $this->_updateButton('save', 'label', Mage::helper('dealerlocator')->__('Product'));
        $this->_removeButton('delete');
        $this->_removeButton('back');

        $this->_blockGroup = 'dealerlocator';
        $this->_controller = 'adminhtml';
        $this->_mode = 'product';
    }

    public function getHeaderText() {
        return Mage::helper('dealerlocator')->__('Add dealers');
    }
}
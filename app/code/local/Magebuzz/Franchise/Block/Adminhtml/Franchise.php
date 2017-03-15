<?php
class Magebuzz_Franchise_Block_Adminhtml_Franchise extends Mage_Adminhtml_Block_Widget_Grid_Container{
    public function __construct(){
        $this->_controller = 'adminhtml_franchise';
        $this->_blockGroup = 'franchise';
        $this->_headerText = Mage::helper('franchise')->__('Application');
        parent::__construct();
        $this->_removeButton('add');
    }
}
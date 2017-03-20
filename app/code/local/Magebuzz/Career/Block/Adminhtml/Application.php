<?php
class Magebuzz_Career_Block_Adminhtml_Application extends Mage_Adminhtml_Block_Widget_Grid_Container{
    public function __construct(){
        $this->_controller = 'adminhtml_application';
        $this->_blockGroup = 'career';
        $this->_headerText = Mage::helper('career')->__('Manage Application');
        parent::__construct();
        $this->_removeButton('add');
    }
}
<?php
class Magebuzz_Career_Block_Adminhtml_Worktype extends Mage_Adminhtml_Block_Widget_Grid_Container{
    public function __construct(){
        $this->_controller = 'adminhtml_worktype';
        $this->_blockGroup = 'career';
        $this->_headerText = Mage::helper('career')->__('Manage Work Type');
        parent::__construct();
    }
}
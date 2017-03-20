<?php
class Magebuzz_Career_Block_Adminhtml_Job extends Mage_Adminhtml_Block_Widget_Grid_Container{
    public function __construct(){
        $this->_controller = 'adminhtml_job';
        $this->_blockGroup = 'career';
        $this->_headerText = Mage::helper('career')->__('Manage Job');
        parent::__construct();
    }
}
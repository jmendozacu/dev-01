<?php

class MarginFrame_Dataflow_Block_Adminhtml_Profile_Import
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize class prefixes and labels
     */
    public function __construct()
    {
        $this->_blockGroup = 'marginframe_dataflow';
        $this->_controller = 'adminhtml_profile_import';
        parent::__construct();
        $this->_headerText = $this->__('Import Profiles');
    }
}

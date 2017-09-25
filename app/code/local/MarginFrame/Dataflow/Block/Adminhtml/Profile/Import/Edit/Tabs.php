<?php

class MarginFrame_Dataflow_Block_Adminhtml_Profile_Import_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('marginframe_dataflow');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Dataflow Import'));
    }
    
    protected function _prepareLayout()
    {
        /** @var MarginFrame_Dataflow_Model_Profile_Import $profile */
        $profile = Mage::registry('profile');

        $this->addTab('general', array(
            'label'     => $this->__('General Information'),
            'title'     => $this->__('General Information'),
            'content'   => $this->getLayout()
                ->createBlock('marginframe_dataflow/adminhtml_profile_import_edit_tab_general')
                ->toHtml(),
        ));
        $this->addTab('mapping', array(
            'label'     => $this->__('Custom Column Mapping'),
            'title'     => $this->__('Custom Column Mapping'),
            'content'   => $this->getLayout()
                ->createBlock('marginframe_dataflow/adminhtml_profile_import_edit_tab_mapping')
                ->toHtml(),
            'is_hidden' => !$profile->getData('custom_column_mapping')
        ));
        $this->addTab('schedule', array(
            'label'     => $this->__('Schedule'),
            'title'     => $this->__('Schedule'),
            'content'   => $this->getLayout()
                ->createBlock('marginframe_dataflow/adminhtml_profile_import_edit_tab_schedule')
                ->toHtml(),
        ));
    }
}

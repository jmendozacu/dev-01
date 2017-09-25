<?php

class MarginFrame_Dataflow_Block_Adminhtml_Profile_Import_Edit_Tab_Mapping
    extends Mage_Adminhtml_Block_Abstract
{
    /**
     * @return MarginFrame_Dataflow_Model_Profile_Import
     */
    public function getProfile()
    {
        return Mage::registry('profile');
    }

    protected function _construct()
    {
        $this->setData('template', 'marginframe/dataflow/import/profile/edit/mapping.phtml');
        parent::_construct();
    }

    /**
     * @return array
     */
    public function getMapping()
    {
        return $this->getProfile()->getMapping();
    }
}

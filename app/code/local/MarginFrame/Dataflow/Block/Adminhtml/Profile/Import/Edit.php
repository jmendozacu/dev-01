<?php

class MarginFrame_Dataflow_Block_Adminhtml_Profile_Import_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        /** @var $profile MarginFrame_Dataflow_Model_Profile_Import */
        $profile = Mage::registry('profile');

        parent::__construct();
        $this->_blockGroup    = 'marginframe_dataflow';
        $this->_controller    = 'adminhtml_profile_import';
        $this->_mode          = 'edit';
        $this->_addButton('saveAndRun', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Run Profile'),
            'onclick'   => '$(editForm.formId).action = (\'' . $this->getUrl('*/*/saveAndRun', array('id' => $profile->getId())) . '\'); editForm.submit();',
            'class'     => 'success',
        ), 100);
        if ($profile->getId()) {
            $this->_addButton('run', array(
                'label'     => Mage::helper('adminhtml')->__('Run Profile'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/run', array('id' => $profile->getId())) . '\');',
                'class'     => 'success',
            ), 110);
        }
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('Dataflow Import');
    }

}

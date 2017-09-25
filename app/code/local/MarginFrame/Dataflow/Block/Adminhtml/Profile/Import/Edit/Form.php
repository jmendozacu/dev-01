<?php

class MarginFrame_Dataflow_Block_Adminhtml_Profile_Import_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        /** @var $profile MarginFrame_Dataflow_Model_Import_Profile */
        $profile = Mage::registry('profile');
        $formUrl = $this->getUrl('*/*/save', array('id' => $profile->getId()));
        $form    = new Varien_Data_Form(array(
            'id'        =>  'edit_form',
            'action'    =>  $formUrl,
            'method'    =>  'post',
            'enctype'   =>  'multipart/form-data'
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}

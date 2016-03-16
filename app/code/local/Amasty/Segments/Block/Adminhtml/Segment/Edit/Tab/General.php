<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Segments
 */ 
class Amasty_Segments_Block_Adminhtml_Segment_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        /* @var $hlp Amasty_Segments_Helper_Data */
        $hlp = Mage::helper('amsegments');
    
        $fldInfo = $form->addFieldset('general', array('legend'=> $hlp->__('General')));
        
        $fldInfo->addField('name', 'text', array(
            'label'     => $hlp->__('Name'),
            'required'  => true,
            'name'      => 'name',
        ));
        
        $fldInfo->addField('is_active', 'select', array(
            'label'     => $hlp->__('Is Active'),
            'name'      => 'is_active',
            'options'    => $hlp->getSegmentStatuses(),
        ));
        
        $fldInfo->addField('website_id', 'select', array(
            'label'     => $hlp->__('Website'),
            'name'      => 'website_id',
            'options'    => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
        ));
                 
        //set form values
        $form->setValues($this->getModel()); 
        
        return parent::_prepareForm();
    }
}
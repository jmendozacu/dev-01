<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */ 
class Amasty_Payrestriction_Block_Adminhtml_Rule_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        /* @var $hlp Amasty_Payrestriction_Helper_Data */
        $hlp = Mage::helper('ampayrestriction');
    
        $fldInfo = $form->addFieldset('general', array('legend'=> $hlp->__('General')));
        $fldInfo->addField('name', 'text', array(
            'label'     => $hlp->__('Name'),
            'required'  => true,
            'name'      => 'name',
        ));
        $fldInfo->addField('is_active', 'select', array(
            'label'     => Mage::helper('salesrule')->__('Status'),
            'name'      => 'is_active',
            'options'    => $hlp->getStatuses(),
        ));  
            
        $fldInfo->addField('methods', 'multiselect', array(
            'label'     => $hlp->__('Methods'),
            'name'      => 'methods[]',
            'values'    => $hlp->getAllMethods(),
            'required'  => true,
        ));

        $fldInfo->addField('coupon', 'text', array(
            'label'     => $hlp->__('Coupon Code'),
            'name'      => 'coupon',
            'note'      => $hlp->__('Apply this payment restriction when specified coupon is provided. You can configure coupon in promotions / shopping cart rules. Useful when you have ONE coupon only.'),
        ));

        $fldInfo->addField('discount_id', 'select', array(
            'label'     => $hlp->__('Shopping Cart Rule (discount)'),
            'name'      => 'discount_id',
            'values'    => $hlp->getAllRules(),
            'note'      => $hlp->__('Apply this restriction with ANY coupon from specified discount rule. See promotions / shopping cart rules. Useful when you have MULTIPLE coupons in one rule.'),
        ));

        
//        $fldInfo->addField('message', 'text', array(
//            'label'     => $hlp->__('Error Message'),
//            'name'      => 'message',
//        ));
        
        //set form values
        $form->setValues(Mage::registry('ampayrestriction_rule')->getData()); 
        
        return parent::_prepareForm();
    }
}
<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
class Amasty_Smtp_Block_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        //$url = $this->getUrl('amsmtp/adminhtml_login/clearlock');

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel('Autofill')
            ->setStyle('float: right; margin-right: -150px; margin-top: -47px;')
            //->setOnClick("setLocation('$url')")
            ->toHtml();

        return $html;
    }
}
<?php


class MarginFrame_KSmart_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('marginframe/ksmart/form.phtml');
        parent::_construct();
    }
}
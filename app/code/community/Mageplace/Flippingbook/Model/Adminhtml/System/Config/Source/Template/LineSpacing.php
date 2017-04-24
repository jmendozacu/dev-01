<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Model_Adminhtml_System_Config_Source_Template_LineSpacing
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $fonts = Mage::helper('flippingbook')->getLineSpacing();
            foreach ($fonts as $key => $value) {
                $this->_options[$key] = $value;
            }
        }
        return $this->_options;
    }

    public function getLabel($value)
    {
        $options = $this->toOptionArray();
        if (isset($options[$value])) return $options[$value];

        return '';
    }
}
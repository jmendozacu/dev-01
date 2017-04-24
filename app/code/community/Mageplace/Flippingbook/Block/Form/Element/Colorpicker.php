<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Form_Element_Colorpicker extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setType('text');
        $this->setExtType('textfield');
        if (isset($attributes['value'])) {
            $this->setValue($attributes['value']);
        }
    }


    public function getElementHtml()
    {
        $this->addClass('input-text');

        $html = sprintf(
            '#<input name="%s" id="%s" value="%s" %s style="width:110px !important;" />',
            $this->getName(), $this->getHtmlId(), $this->_escape($this->getValue()), $this->serialize($this->getHtmlAttributes())
        );

        $html .= sprintf('
            <script type="text/javascript">
            //<![CDATA[
                new Control.ColorPicker("%s");
            //]]>
            </script>',
            $this->getHtmlId()
        );

        $html .= $this->getAfterElementHtml();

        return $html;
    }
}
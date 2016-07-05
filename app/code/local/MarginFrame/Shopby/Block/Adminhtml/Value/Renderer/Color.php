<?php
class MarginFrame_Shopby_Block_Adminhtml_Value_Renderer_Color extends  Varien_Data_Form_Element_Abstract{
    public function getElementHtml() {
        $html = parent::getElementHtml();
        $html .= "<script type=\"text/javascript\">
            $('".$this->getHtmlId()."').color = new jscolor.color($('".$this->getHtmlId()."'));
        </script>";
        return $html;
    }
}

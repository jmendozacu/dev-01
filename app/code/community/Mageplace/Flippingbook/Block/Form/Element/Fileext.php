<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Form_Element_Fileext extends Varien_Data_Form_Element_Abstract
{

    public function __construct($data)
    {
        parent::__construct($data);
        $this->setType('file');
    }


    public function getElementHtml()
    {
        $html = '';

        if ($this->getValue()) {
            $url = $this->_getUrl();
            $html = '<a href="'.$url.'">'.($this->getTitle() ? $this->getTitle() : $this->getValue()).'</a> ';
        }
        $this->setClass('input-file');
        $html.= parent::getElementHtml();
        $html.= $this->_getDeleteCheckbox();

        return $html;
    }


    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($this->getValue()) {
            $html .= '<span class="delete-image">';
            $html .= '<input type="checkbox" name="'.parent::getName().'[delete]" value="1" class="checkbox" id="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' disabled="disabled"': '').'/>';
            $html .= '<label for="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' class="disabled"' : '').'> Delete File</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }

        return $html;
    }


    protected function _getHiddenInput()
    {
        return '<input type="hidden" name="'.parent::getName().'[value]" value="'.$this->getValue().'" />';
    }


    protected function _getUrl()
    {
        return $this->getValue();
    }


    public function getName()
    {
        return  $this->getData('name');
    }

}

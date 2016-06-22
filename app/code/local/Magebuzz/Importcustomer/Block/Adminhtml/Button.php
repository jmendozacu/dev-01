<?php
class Magebuzz_Importcustomer_Block_Adminhtml_Button extends Mage_Adminhtml_Block_System_Config_Form_Field{
  protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
    $this->setElement($element);
    $url = Mage::helper('adminhtml')->getUrl('importcustomer/adminhtml_importcustomer/index');
    
    $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                 ->setType('button')
                 ->setClass('scalable')
                 ->setLabel('Force Import Now !')
                 ->setOnClick("setLocation('$url')")
                 ->toHtml();

    return $html;
  }
}
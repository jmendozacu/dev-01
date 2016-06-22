<?php
class Magebuzz_CSPB_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Special extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Special {
  public function getElementHtml() {
		$html = '<input id="'.$this->getElement()->getHtmlId().'" name="'.$this->getElement()->getName()
			.'" value="'.$this->getElement()->getEscapedValue().'" '.$this->getElement()->serialize($this->getElement()->getHtmlAttributes()).'/>'."\n"
			.'<strong>[' . Mage::app()->getStore()->getBaseCurrencyCode() . ']</strong>';;
		return $html;
	}
}

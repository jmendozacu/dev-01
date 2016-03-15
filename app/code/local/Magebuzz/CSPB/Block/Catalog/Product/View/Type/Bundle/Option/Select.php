<?php
class Magebuzz_CSPB_Block_Catalog_Product_View_Type_Bundle_Option_Select
    extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Select
{
    public function getSelectionQtyTitlePrice($_selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection);
        $this->setFormatProduct($_selection);
        
        //$priceTitle = $_selection->getSelectionQty() * 1 . ' x ' . $this->escapeHtml($_selection->getName());
        
        $priceTitle = $this->escapeHtml($_selection->getName()).' &nbsp; Qty : '. $_selection->getSelectionQty() * 1 ;

        /* $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
            . '+' . $this->formatPriceString($price, $includeContainer)
            . ($includeContainer ? '</span>' : ''); */
            
        $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice" style="display:none">' : '')
            . '' . $this->formatPriceString($price, $includeContainer)
            . ($includeContainer ? '</span>' : '');

        return $priceTitle;
    }

    public function getSelectionTitlePrice($_selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection, 1);
        $this->setFormatProduct($_selection);
        $priceTitle = $this->escapeHtml($_selection->getName());
        
        /* $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
            . '+' . $this->formatPriceString($price, $includeContainer)
            . ($includeContainer ? '</span>' : ''); */
            
        if( $price != 0){
          $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
              . '' . $this->formatPriceString($price, $includeContainer)
              . ($includeContainer ? '</span>' : '');
        }    
        
        return $priceTitle;
    }

    public function formatPriceString($price, $includeContainer = true)
    {
        $taxHelper  = Mage::helper('tax');
        $coreHelper = $this->helper('core');
        $currentProduct = $this->getProduct();
        if ($currentProduct->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC
                && $this->getFormatProduct()
        ) {
            $product = $this->getFormatProduct();
        } else {
            $product = $currentProduct;
        }

        $priceTax    = $taxHelper->getPrice($product, $price);
        $priceIncTax = $taxHelper->getPrice($product, $price, true);

        $formated = $coreHelper->currencyByStore($priceTax, $product->getStore(), true, $includeContainer);
        if ($taxHelper->displayBothPrices() && $priceTax != $priceIncTax) {
            $formated .=
                    ' (' .
                    $coreHelper->currencyByStore($priceIncTax, $product->getStore(), true, $includeContainer) .
                    ' ' . $this->__('Incl. Tax') . ')';
        }

        return $formated;
    }
}

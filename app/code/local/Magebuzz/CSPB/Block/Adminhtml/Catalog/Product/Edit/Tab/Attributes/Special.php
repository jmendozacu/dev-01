<?php
class Magebuzz_CSPB_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Special extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Special
{
    public function getElementHtml(){
      /* $html = '<input id="'.$this->getElement()->getHtmlId().'" name="'.$this->getElement()->getName()
           .'" value="'.$this->getElement()->getEscapedValue().'" '.$this->getElement()->serialize($this->getElement()->getHtmlAttributes()).'/>'."\n"
           .'<strong>[%]</strong>';; */
           
      $id = $this->getElement()->getHtmlId();
      
      $html = '<input id="'.$id.'_baht" value="" '.$this->getElement()->serialize($this->getElement()->getHtmlAttributes()).'/>'
      . '<strong>[THB]</strong>'
      . '<input id="'.$id.'" name="'.$this->getElement()->getName()
      . '" value="'.$this->getElement()->getValue().'" style="display:none" />'."\n"

      ."<script type='text/javascript'>
        
        Number.prototype.round = function(places) {
          return +(Math.round(this + 'e+' + places)  + 'e-' + places);
        }

        if(document.getElementById('{$id}').value.trim() != ''){
          var sp_percent = parseFloat(document.getElementById('{$id}').value);
          var price = parseFloat(document.getElementById('price').value);
          document.getElementById('{$id}_baht').value = parseFloat(sp_percent/100.00 * price).round(0)+'.00';
        }

        document.getElementById('{$id}_baht').onkeyup = function () {
          if(document.getElementById('{$id}_baht').value.trim() == ''){
            document.getElementById('{$id}').value = '';
          } else {
            var sp_baht = parseFloat(document.getElementById('{$id}_baht').value);
            var price = parseFloat(document.getElementById('price').value);
            document.getElementById('{$id}').value = parseFloat(sp_baht/price * 100.00);
          }
        }

        document.getElementById('price').onkeyup = function () {
          var price = parseFloat(document.getElementById('price').value);
          
          /* spacial price */
          if(document.getElementById('{$id}_baht').value.trim() == ''){
            document.getElementById('{$id}').value = '';
          } else {
            var sp_baht = parseFloat(document.getElementById('{$id}_baht').value);
            
            document.getElementById('{$id}').value = parseFloat(sp_baht/price * 100.00);
          }
          
          /* group price */
          $$('.group-price-baht').each(function(elm) {
            var sp_baht = parseFloat($(elm).value);
            $(elm).next().value = parseFloat(sp_baht/price * 100.00);
          });
          
          /* tier price */
          $$('.tier-price-baht').each(function(elm) {
            var sp_baht = parseFloat($(elm).value);
            $(elm).next().value = parseFloat(sp_baht/price * 100.00);
          });
          
        }

      </script>"
      ;
    
      return $html;
    }
}

<?php
class MarginFrame_Shopby_Block_Adminhtml_Value_Renderer_Preview extends  Varien_Data_Form_Element_Abstract{
    public function getElementHtml() {
      $html = '<input id="'.$this->getHtmlId().'" name="'.$this->getName()
        .'" value="'.$this->getEscapedValue().'" type="hidden" />'."\n";
      $html.= $this->getAfterElementHtml();
      $html .= '<div id="preview_color" style="width: 20px;height: 20px;background: rgb(255, 0, 0);"></div>';
      $script =
        '<script>
          var bg = $("color_swatch").value;
          $("preview_color").setStyle({background:"#"+bg});
          $("color_swatch").observe("change",function(){
             var bg = $("color_swatch").getStyle("background");
              $("preview_color").setStyle({background:bg});
          });
        </script>';
      return $html.$script;
    }
}

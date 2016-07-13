<?php
class MarginFrame_Custom_Block_Adminhtml_Catalog_Product_Renderer_Hasimage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        try {
			$val = Mage::helper('catalog/image')->init($row, 'image');
			if(strrpos($val, 'indexliving_image_placeholder') !== false){
				return 'No';
			}
			return 'Yes';
        } catch (Exception $e) {
            return 'No';
        } 
    }

   //  protected function _getValue(Varien_Object $row)
   //  {
   //      try {
			// $val = Mage::helper('catalog/image')->init($row, 'thumbnail')->resize(97);
			// if(strrpos($val, 'indexliving_image_placeholder') !== false){
			// 	return 0;
			// }
			// return 1;
   //      } catch (Exception $e) {
   //          return 0;
   //      }   
   //  }
}
<?php
class Magebuzz_Customreview_Block_Adminhtml_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    /**
     * Thumbnail images renderer
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        $model = Mage::getModel('review/review')->load($row->getData('review_id'));
        $imgName = $model->getData('img');
        if($imgName){
        return '<img width="150" src="' . Mage::getBaseUrl('media').'review/' . $imgName. '" />';
        }

    }
}
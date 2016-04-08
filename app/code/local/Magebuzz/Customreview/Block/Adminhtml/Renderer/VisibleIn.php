<?php
class Magebuzz_Customreview_Block_Adminhtml_Renderer_VisibleIn extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    /**
     * Thumbnail images renderer
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        $model = Mage::getModel('review/review')->load($row->getData('review_id'));
        $store_ids = $model->getData('stores');
        $store = Mage::getModel('core/store')->load($store_ids['1']);
        $name = $store->getName();
        return $name;
    }
}
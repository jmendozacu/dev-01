<?php

class MarginFrame_Dataflow_Block_Adminhtml_Profile_Import_Grid_Renderer_Actions
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return mixed
     */
    public function _getValue(Varien_Object $row)
    {
        return sprintf('<a href="%s">%s</a>',
            $this->getUrl('*/*/run', array('id' => $row->getId())),
            $this->__('Run Profile')
        );
    }
}

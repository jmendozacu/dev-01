<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */

/**
 * @author MarginFrame
 */ 
class MarginFrame_Shopby_Block_Adminhtml_Range_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id'; 
        $this->_blockGroup = 'amshopby';
        $this->_controller = 'adminhtml_range';
    }

    public function getHeaderText()
    {
            return Mage::helper('amshopby')->__('Range');
    }
}
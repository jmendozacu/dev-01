<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */

/**
 * @author MarginFrame
 */   
class MarginFrame_Shopby_Block_Adminhtml_Page extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_page';
    $this->_blockGroup = 'amshopby';
    $this->_headerText = Mage::helper('amshopby')->__('Pages');
    $this->_addButtonLabel = Mage::helper('amshopby')->__('Add Page');
    parent::__construct();
  }
}



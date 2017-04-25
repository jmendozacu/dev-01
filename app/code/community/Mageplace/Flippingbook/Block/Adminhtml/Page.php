<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Page extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    
    public function __construct()
    {
        $this->_blockGroup = 'flippingbook';
        $this->_controller = 'adminhtml_page';

        $this->_addButtonLabel = $this->__('Add New Page');

        parent::__construct();
    }

    public function getHeaderText()
    {
        return $this->__('Manage Pages');
    }

    public function getHeaderCssClass()
    {
        return '';
    }

}

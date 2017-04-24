<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Magazine extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'flippingbook';
        $this->_controller = 'adminhtml_magazine';

        $this->_addButtonLabel = $this->__('Add New Book');

        parent::__construct();
    }

    public function getHeaderText()
    {
        return $this->__('Manage Books');
    }


    public function getHeaderCssClass()
    {
        return '';
    }


}

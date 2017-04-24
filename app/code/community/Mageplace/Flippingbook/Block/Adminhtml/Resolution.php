<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Resolution extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'flippingbook';
        $this->_controller = 'adminhtml_resolution';

        $this->_addButtonLabel = $this->__('Add New Resolution');

        parent::__construct();
    }

    public function getHeaderText()
    {
        return $this->__('Manage Resolutions');
    }


    public function getHeaderCssClass()
    {
        return '';
    }


}

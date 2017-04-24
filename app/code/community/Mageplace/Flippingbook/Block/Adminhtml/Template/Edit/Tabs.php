<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Template_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('flippingbook_template_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('flippingbook')->__('Template Information'));
    }
}
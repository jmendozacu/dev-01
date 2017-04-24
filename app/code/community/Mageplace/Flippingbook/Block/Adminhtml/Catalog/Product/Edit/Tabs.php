<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Catalog_Product_Edit_Tabs
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return $this->__('HTML5 Flipping Book');
    }

    public function getTabTitle()
    {
        return $this->__('HTML5 Flipping Book');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getTabClass()
    {
        return 'ajax';
    }

    public function getSkipGenerateContent()
    {
       return true;
    }

    public function getTabUrl()
    {
        return $this->getUrl('*/flippingbook/product', array('_current' => true));
    }


}

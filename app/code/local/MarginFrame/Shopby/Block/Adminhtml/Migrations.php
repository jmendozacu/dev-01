<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */ 
class MarginFrame_Shopby_Block_Adminhtml_Migrations extends Mage_Adminhtml_Block_Template
{
    protected function getInfo()
    {
        /** @var MarginFrame_Shopby_Helper_Migration $helper */
        $helper = Mage::helper('amshopby/migration');

        return $helper->getMigrationsInfo();
    }

    protected function getRealStateVersion()
    {
        /** @var MarginFrame_Shopby_Helper_Migration $helper */
        $helper = Mage::helper('amshopby/migration');

        return $helper->getRealStateVersion();
    }
}

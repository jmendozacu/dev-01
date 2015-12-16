<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */

class MarginFrame_Shopby_Helper_Layer_View_Strategy_Category extends MarginFrame_Shopby_Helper_Layer_View_Strategy_Abstract
{
    public function prepare()
    {
        parent::prepare();

        $this->filter->setDisplayType(Mage::getStoreConfig('amshopby/general/categories_type'));
    }

    protected function setTemplate()
    {
        return 'marginframe/amshopby/category.phtml';
    }

    protected function setPosition()
    {
        return Mage::getStoreConfig('amshopby/general/categories_order');
    }

    protected function setHasSelection()
    {
        $result = !!Mage::app()->getRequest()->getParam('cat');
        return $result;
    }

    protected function setCollapsed()
    {
        return $this->isCollapseEnabled() && Mage::getStoreConfig('amshopby/general/categories_collapsed');
    }

    public function getIsExcluded()
    {
        return $this->setPosition() == -1;
    }
}

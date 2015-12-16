<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */

class MarginFrame_Shopby_Helper_Layer_View_Strategy_Rating extends MarginFrame_Shopby_Helper_Layer_View_Strategy_Abstract
{

    public function prepare()
    {
        parent::prepare();

        $this->filter->setData('hide_counts', !Mage::getStoreConfig('catalog/layered_navigation/display_product_count'));
    }

    protected function setTemplate()
    {
        return 'marginframe/amshopby/attribute.phtml';
    }

    protected function setPosition()
    {
        return $this->filter->getPosition();
    }

    protected function setHasSelection()
    {
        return isset($_GET['rating']);
    }

    protected function setCollapsed()
    {
        return $this->isCollapseEnabled() && Mage::getStoreConfig('amshopby/general/rating_collapsed');
    }
}

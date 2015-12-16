<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Shopby
 */  
class MarginFrame_Shopby_Model_Source_Position extends MarginFrame_Shopby_Model_Source_Abstract
{
    const LEFT = 'left';
    const TOP = 'top';
    const BOTH = 'both';

    public function toOptionArray()
    {
        $hlp = Mage::helper('amshopby');
        return array(
            array('value' => self::LEFT, 'label' => $hlp->__('Sidebar')),
            array('value' => self::TOP,  'label' => $hlp->__('Top')),
            array('value' => self::BOTH, 'label' => $hlp->__('Both')),
        );
    }
    
}
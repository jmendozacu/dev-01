<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Magpleasure_Common
 */

class Magpleasure_Common_Block_System_Entity_Form_Element_Ajax_Tags extends Varien_Data_Form_Element_Abstract
{

    public function getElementHtml()
    {
        $select = new Magpleasure_Common_Block_System_Entity_Form_Element_Ajax_Tags_Render($this->getData());
        $select->setLayout(Mage::app()->getLayout());

        if (Mage::registry('current_product')){            
            $select->setData('name', 'product['.$select->getName().']');
        }

        $html = '';
        $html .= $select->toHtml();

        $html.= $this->getAfterElementHtml();
        return $html;
    }
}
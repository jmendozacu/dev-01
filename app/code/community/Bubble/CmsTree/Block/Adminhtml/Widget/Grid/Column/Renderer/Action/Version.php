<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Block_Adminhtml_Widget_Grid_Column_Renderer_Action_Version
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        $actions = $this->getColumn()->getActions();
        if (empty($actions) || !is_array($actions)) {
            return '&nbsp;';
        }

        $out = array();
        foreach ($actions as $action) {
            if (is_array($action)) {
                $out[] = $this->_toLinkHtml($action, $row);
            }
        }

        return implode('<br />', $out);
    }
}
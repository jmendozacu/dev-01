<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
class Amasty_Smtp_Block_Adminhtml_Debug extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'amsmtp';
        $this->_controller = 'adminhtml_debug';
        $this->_headerText = Mage::helper('cms')->__('Debug Log');
        parent::__construct();
        $this->removeButton('add');
    }

    protected function _prepareLayout()
    {
        $script = "
            if (confirm('".Mage::helper('amsmtp')->__('Are you sure?')."'))
                window.location.href='".$this->getUrl('adminhtml/amsmtp/cleardebug')."';
        ";

        $this->addButton('clear', array(
            'label' => Mage::helper('amsmtp')->__('Clear Debug Log'),
            'onclick' => $script,
            'class' => 'delete',
        ));

        return parent::_prepareLayout();
    }
}

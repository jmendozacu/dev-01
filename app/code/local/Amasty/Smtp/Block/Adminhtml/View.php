<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
class Amasty_Smtp_Block_Adminhtml_View extends Mage_Adminhtml_Block_Widget_View_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'window.location.href=\'' . $this->getUrl('*/*/log') . '\'',
            'class'     => 'back',
        ));
        $this->_removeButton('edit');
        $this->_headerText = 'E-mail Message View';
    }

    protected function _prepareLayout()
    {
        $this->setChild('plane', $this->getLayout()->createBlock('amsmtp/adminhtml_view_message'));
        return Mage_Adminhtml_Block_Widget_Container::_prepareLayout();
    }
}
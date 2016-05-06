<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
class Amasty_Smtp_Block_Adminhtml_Debug_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('amsmtpLogGrid');
        $this->setDefaultSort('cdate');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('amsmtp/debug')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('cdate', array(
            'header'    => Mage::helper('amsmtp')->__('Date'),
            'align'     => 'left',
            'index'     => 'cdate',
            'type'      => 'datetime',
        ));

        $this->addColumn('message', array(
            'header'    => Mage::helper('amsmtp')->__('Record'),
            'index'     => 'message',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return '';
    }
}

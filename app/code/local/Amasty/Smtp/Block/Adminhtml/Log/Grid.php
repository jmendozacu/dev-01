<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
class Amasty_Smtp_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $collection = Mage::getModel('amsmtp/log')->getCollection();
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

        $this->addColumn('subject', array(
            'header'    => Mage::helper('amsmtp')->__('Subject'),
            'index'     => 'subject',
        ));

        $this->addColumn('recipient_email', array(
            'header'    => Mage::helper('amsmtp')->__('Recipient E-mail'),
            'index'     => 'recipient_email',
        ));

        $this->addColumn('recipient_name', array(
            'header'    => Mage::helper('amsmtp')->__('Recipient Name'),
            'index'     => 'recipient_name',
        ));

        $this->addColumn('template_code', array(
            'header'    => Mage::helper('amsmtp')->__('Template Code'),
            'index'     => 'template_code',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('amsmtp')->__('Status'),
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('amsmtp/log')->getAvailableStatuses()
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('log_id' => $row->getId()));
    }
}

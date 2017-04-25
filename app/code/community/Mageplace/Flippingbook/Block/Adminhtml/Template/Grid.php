<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('flippingbook_template_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('template_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('flippingbook/template_collection');
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }


    protected function _prepareColumns()
    {
        $this->addColumn('template_id',
            array(
                'header' => $this->__('Template ID'),
                'width'  => '80px',
                'type'   => 'number',
                'index'  => 'template_id'
            )
        );

        $this->addColumn(
            'template_name',
            array(
                'header' => $this->__('Template Name'),
                'index'  => 'template_name',
            )
        );

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('adminhtml')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('adminhtml')->__('Edit'),
                        'url'     => array(
                            'base' => '*/*/edit'
                        ),
                        'field'   => 'template_id'
                    ),
                    array(
                        'caption' => Mage::helper('adminhtml')->__('Delete'),
                        'url'     => array(
                            'base' => '*/*/delete'
                        ),
                        'field'   => 'template_id'
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
            )
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('template_id');
        $this->getMassactionBlock()->setFormFieldName('templatetable');

        $this->getMassactionBlock()
            ->addItem('delete',
                array(
                    'label' => Mage::helper('adminhtml')->__('Delete'),
                    'url'   => $this->getUrl('*/*/massDelete')
                )
            );


        return $this;
    }


    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('template_id' => $row->getId()));
    }
}
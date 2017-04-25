<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Resolution_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('flippingbook_resolution_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('resolution_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }


    protected function _prepareCollection()
    {

        $collection = Mage::getResourceModel('flippingbook/resolution_collection');
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }


    protected function _prepareColumns()
    {
        $this->addColumn('resolution_id',
            array(
                'header' => $this->__('Resolution ID'),
                'width'  => '80px',
                'type'   => 'number',
                'index'  => 'resolution_id'
            )
        );

        $this->addColumn(
            'resolution_name',
            array(
                'header' => $this->__('Resolution Name'),
                'index'  => 'resolution_name',
            )
        );

        $this->addColumn(
            'resolution_width',
            array(
                'header' => $this->__('Resolution Width'),
                'index'  => 'resolution_width',
                'type'   => 'number',
                'style'  => 'width:20px!important;',
            )
        );

        $this->addColumn(
            'resolution_height',
            array(
                'header' => $this->__('Resolution Height'),
                'index'  => 'resolution_height',
                'type'   => 'number',
                'style'  => 'width:20px!important;',
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
                        'field'   => 'resolution_id'
                    ),
                    array(
                        'caption' => Mage::helper('adminhtml')->__('Delete'),
                        'url'     => array(
                            'base' => '*/*/delete'
                        ),
                        'field'   => 'resolution_id'
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
        $this->setMassactionIdField('resolution_id');
        $this->getMassactionBlock()->setFormFieldName('resolutiontable');

        $this->getMassactionBlock()->addItem('delete',
            array(
                'label' => Mage::helper('adminhtml')->__('Delete'),
                'url'   => $this->getUrl('*/*/massDelete')
            )
        );

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('resolution_id' => $row->getId()));
    }


}

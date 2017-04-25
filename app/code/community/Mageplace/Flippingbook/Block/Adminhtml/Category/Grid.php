<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('flippingbook_category_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('category_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('flippingbook/category_collection');
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }


    protected function _prepareColumns()
    {
        $this->addColumn('category_id',
            array(
                'header' => $this->__('Category ID'),
                'width'  => '80px',
                'type'   => 'number',
                'index'  => 'category_id'
            )
        );

        $this->addColumn(
            'category_name',
            array(
                'header' => $this->__('Category Name'),
                'index'  => 'category_name',
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
                        'field'   => 'category_id'
                    ),
                    array(
                        'caption' => Mage::helper('adminhtml')->__('Delete'),
                        'url'     => array(
                            'base' => '*/*/delete'
                        ),
                        'field'   => 'category_id'
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
        $this->setMassactionIdField('category_id');
        $this->getMassactionBlock()->setFormFieldName('categorytable');

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
        return $this->getUrl('*/*/edit', array('category_id' => $row->getId()));
    }
}

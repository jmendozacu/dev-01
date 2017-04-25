<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Page_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('flippingbook_page_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('page_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }


    protected function _prepareCollection()
    {
        /* @var $collection Mageplace_Flashmagazine_Model_Mysql4_Page_Collection */
        $collection = Mage::getResourceModel('flippingbook/page_collection');
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }


    protected function _prepareColumns()
    {
        $this->addColumn('page_id',
            array(
                'header'	=> $this->__('Page ID'),
                'width'		=> '80px',
                'type'		=> 'number',
                'index'		=> 'page_id'
            )
        );

        $this->addColumn(
            'page_title',
            array(
                'header'	=> $this->__('Page Name'),
                'index'		=> 'page_title',
            )
        );


        $this->addColumn('magazine_id',
            array(
                'header'					=> $this->__('Page Book'),
                'index'						=> 'page_magazine_id',
                'type'						=> 'options',
                'width'						=> '200px',
                'options'					=> $this->_getMagazine(),
                'sortable'					=> false,
                'filter_condition_callback'	=> array(
                    $this,
                    '_filterMagazineCondition'
                )
            )
        );

        $this->addColumn('sort_order',
            array(
                'type'		=> 'number',
                'header'	=> $this->__('Position'),
                'index'		=> 'page_sort_order'
            )
        );

        $this->addColumn('is_active',
            array(
                'type'		=> 'options',
                'header'	=> Mage::helper('cms')->__('Active'),
                'index'		=> 'is_active',
                'width'		=> '70px',
                'options'	=> array(
                    0 => Mage::helper('cms')->__('No'),
                    1 => Mage::helper('cms')->__('Yes')
                )
            )
        );

        $this->addColumn('action',
            array(
                'header'	=> Mage::helper('adminhtml')->__('Action'),
                'width'		=> '50px',
                'type'		=> 'action',
                'getter'	=> 'getId',
                'actions'	=> array(
                    array(
                        'caption'	=> Mage::helper('adminhtml')->__('Edit'),
                        'url'		=> array(
                            'base' => '*/*/edit'
                        ),
                        'field'		=> 'page_id'
                    ),
                    array(
                        'caption'	=> $this->__('Enable/Disable'),
                        'url'		=> array(
                            'base' => '*/*/enable'
                        ),
                        'field'		=> 'page_id'
                    ),
                    array(
                        'caption'	=> Mage::helper('adminhtml')->__('Delete'),
                        'url'		=> array(
                            'base' => '*/*/delete'
                        ),
                        'field'		=> 'page_id'
                    ),
                ),
                'filter'	=> false,
                'sortable'	=> false,
                'is_system'	=> true,
            )
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('page_id');
        $this->getMassactionBlock()->setFormFieldName('pagetable');

        $this->getMassactionBlock()
            ->addItem('enable',
                array(
                    'label'	=> $this->__('Enable/Disable'),
                    'url'	=> $this->getUrl('*/*/massEnable')
                )
            )
            ->addItem('delete',
                array(
                    'label'	=> Mage::helper('adminhtml')->__('Delete'),
                    'url'	=> $this->getUrl('*/*/massDelete')
                )
            );
        ;

        return $this;
    }


    protected function _getMagazine()
    {
        return Mage::getResourceModel('flippingbook/magazine_collection')->toOptionHash();
    }


    protected function _filterMagazineCondition($collection, $column)
    {
        if(!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addMagazineFilter($value);
    }


    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('page_id' => $row->getId()));
    }
}

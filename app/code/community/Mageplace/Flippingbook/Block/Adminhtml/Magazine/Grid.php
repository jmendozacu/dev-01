<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Adminhtml_Magazine_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('flippingbook_magazine_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('magazine_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }


    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('flippingbook/magazine_collection');
        $this->setCollection($collection);
        parent::_prepareCollection();

        return $this;
    }


    protected function _prepareColumns()
    {
        $this->addColumn('magazine_id',
            array(
                'type'                      => 'number',
                'header'                    => $this->__('Book ID'),
                'width'                     => '80px',
                'index'                     => 'magazine_id',
                'filter_condition_callback' => array(
                    $this,
                    '_filterMagazineIdCondition'
                )
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('cms')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }

        $this->addColumn(
            'magazine_name',
            array(
                'type'		=> 'text',
                'header'	=> $this->__('Book Name'),
                'index'		=> 'magazine_title',
            )
        );

        $this->addColumn('magazine_category_id',
            array(
                'type'						=> 'options',
                'header'					=> $this->__('Book Category'),
                'index'						=> 'magazine_category_id',
                'options'					=> $this->_getMagazineCategories(),
                'sortable'					=> false,
                'filter_condition_callback'	=> array(
                    $this,
                    '_filterMagazineCategoryCondition'
                )
            )
        );

        $this->addColumn('magazine_template_id',
            array(
                'type'						=> 'options',
                'header'					=> $this->__('Book Template'),
                'index'						=> 'magazine_template_id',
                'options'					=> $this->_getMagazineTemplates(),
                'sortable'					=> false,
                'filter_condition_callback'	=> array(
                    $this,
                    '_filterMagazineTemplateCondition'
                )
            )
        );

        $this->addColumn('magazine_resolution_id',
            array(
                'type'						=> 'options',
                'header'					=> $this->__('Book Resolution'),
                'index'						=> 'magazine_resolution_id',
                'options'					=> $this->_getMagazineResolutions(),
                'sortable'					=> false,
                'filter_condition_callback'	=> array(
                    $this,
                    '_filterMagazineResolutionCondition'
                )
            )
        );

        $this->addColumn('sort_order',
            array(
                'type'		=> 'number',
                'header'	=> $this->__('Position'),
                'index'		=> 'magazine_sort_order'
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
                'type'		=> 'action',
                'header'	=> Mage::helper('adminhtml')->__('Action'),
                'width'		=> '50px',
                'getter'	=> 'getId',
                'actions'	=> array(
                    array(
                        'caption'	=> Mage::helper('adminhtml')->__('Edit'),
                        'url'		=> array(
                            'base' => '*/*/edit'
                        ),
                        'field'		=> 'magazine_id'
                    ),
                    array(
                        'caption'	=> $this->__('Enable/Disable'),
                        'url'		=> array(
                            'base' => '*/*/enable'
                        ),
                        'field'		=> 'magazine_id'
                    ),
                    array(
                        'caption'	=> Mage::helper('adminhtml')->__('Delete'),
                        'url'		=> array(
                            'base' => '*/*/delete'
                        ),
                        'field'		=> 'magazine_id'
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
        $this->setMassactionIdField('magazine_id');
        $this->getMassactionBlock()->setFormFieldName('magazinetable');

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

        return $this;
    }


    protected function _filterMagazineIdCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addMagazineIdFilter($value);
    }


    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }


    protected function _getMagazineCategories()
    {
        return Mage::getResourceModel('flippingbook/category_collection')->toOptionHash();
    }


    protected function _filterMagazineCategoryCondition($collection, $column)
    {
        if(!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addCategoryFilter($value);
    }

    protected function _getMagazineTemplates()
    {
        return Mage::getResourceModel('flippingbook/template_collection')->toOptionHash();
    }


    protected function _filterMagazineTemplateCondition($collection, $column)
    {
        if(!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addTemplateFilter($value);
    }


    protected function _getMagazineResolutions()
    {
        return Mage::getResourceModel('flippingbook/resolution_collection')->toOptionHash();
    }


    protected function _filterMagazineResolutionCondition($collection, $column)
    {
        if(!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addResolutionFilter($value);
    }


    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('magazine_id' => $row->getId()));
    }
}

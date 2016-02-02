<?php
/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Bannerads_Block_Adminhtml_Images_Grid extends Mage_Adminhtml_Block_Widget_Grid {
  public function __construct() {
    parent::__construct();
    $this->setId('imagesGrid');
    $this->setDefaultSort('banner_id');
    $this->setDefaultDir('DESC');
    $this->setSaveParametersInSession(TRUE);
  }

  protected function _prepareCollection() {
    $collection = Mage::getModel('bannerads/images')->getCollection();
    $this->setCollection($collection);
    return parent::_prepareCollection();
  }

  protected function _prepareColumns() {
    $this->addColumn('banner_id', array('header' => Mage::helper('bannerads')->__('ID'), 'align' => 'right', 'width' => '50px', 'index' => 'banner_id',));

    $this->addColumn('banner_title', array('header' => Mage::helper('bannerads')->__('Title'), 'align' => 'left', 'index' => 'banner_title',));

    $this->addColumn('banner_image', array('header' => Mage::helper('bannerads')->__('Image'), 'index' => 'banner_image', 'align' => 'center', 'renderer' => 'Magebuzz_Bannerads_Block_Adminhtml_Images_Renderer_Images'));

    $this->addColumn('banner_url', array('header' => Mage::helper('bannerads')->__('Banner Url'), 'index' => 'banner_url',));

    $this->addColumn('banner_description', array('header' => Mage::helper('bannerads')->__('Banner Description'), 'index' => 'banner_description',));

    $this->addColumn('sort_order', array('header' => Mage::helper('bannerads')->__('Sort Order'), 'index' => 'sort_order',));

    $this->addColumn('status', array('header' => Mage::helper('bannerads')->__('Status'), 'align' => 'left', 'width' => '80px', 'index' => 'status', 'type' => 'options', 'options' => array(1 => 'Enabled', 2 => 'Disabled',),));

    $this->addColumn('action', array('header' => Mage::helper('bannerads')->__('Action'), 'width' => '100', 'type' => 'action', 'getter' => 'getId', 'actions' => array(array('caption' => Mage::helper('bannerads')->__('Edit'), 'url' => array('base' => '*/*/edit'), 'field' => 'id')), 'filter' => FALSE, 'sortable' => FALSE, 'index' => 'stores', 'is_system' => TRUE,));
    return parent::_prepareColumns();
  }

  protected function _prepareMassaction() {
    $this->setMassactionIdField('banner_id');
    $this->getMassactionBlock()->setFormFieldName('images');

    $this->getMassactionBlock()->addItem('delete', array('label' => Mage::helper('bannerads')->__('Delete'), 'url' => $this->getUrl('*/*/massDelete'), 'confirm' => Mage::helper('bannerads')->__('Are you sure?')));

    $statuses = Mage::getSingleton('bannerads/status')->getOptionArray();

    array_unshift($statuses, array('label' => '', 'value' => ''));
    $this->getMassactionBlock()->addItem('status', array('label' => Mage::helper('bannerads')->__('Change status'), 'url' => $this->getUrl('*/*/massStatus', array('_current' => TRUE)), 'additional' => array('visibility' => array('name' => 'status', 'type' => 'select', 'class' => 'required-entry', 'label' => Mage::helper('bannerads')->__('Status'), 'values' => $statuses))));
    return $this;
  }

  public function getRowUrl($row) {
    return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
}
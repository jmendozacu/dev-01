<?php
class Magebuzz_Career_Block_Adminhtml_Worktype_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    public function __construct() {
        parent::__construct();
        $this->setId('worktype');
        $this->setDefaultSort('worktype_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {

        $collection = Mage::getModel('career/worktype')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns() {
        $this->addColumn('worktype_id', array(
            'header' => Mage::helper('career')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'worktype_id',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('career')->__('Work Type Title'),
            'align' => 'left',
            'index' => 'title',
            'width' => '100px'
        ));

        $this->addColumn('status', array(
          'header'  => Mage::helper('career')->__('Status'),
          'align'   => 'left', 'width' => '80px',
          'index'   => 'status', 'type' => 'options',
          'options' => array(
            1 => 'Enabled',
            0 => 'Disabled',
          ),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('career')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('career')->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id'
                )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();

    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('worktype_id');
        $this->getMassactionBlock()->setFormFieldName('worktype');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('career')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('career')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem('status', array(
          'label'      => Mage::helper('career')->__('Change status'),
          'url'        => $this->getUrl('*/*/massStatus', array('_current' => TRUE)),
          'additional' => array(
            'visibility' => array(
              'name'   => 'status',
              'type'   => 'select',
              'class'  => 'required-entry',
              'label'  => Mage::helper('career')->__('Status'),
              'values' => array(
                '1' => 'Enable',
                '0' => 'Disable'
              )
            )
          )
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
                'store'=>$this->getRequest()->getParam('store'),
                'id'=>$row->getId())
        );
    }
}
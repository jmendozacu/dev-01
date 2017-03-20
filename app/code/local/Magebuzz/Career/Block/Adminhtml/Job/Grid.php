<?php
class Magebuzz_Career_Block_Adminhtml_Job_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    public function __construct() {
        parent::__construct();
        $this->setId('job');
        $this->setDefaultSort('job_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {

        $collection = Mage::getModel('career/job')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns() {
        $this->addColumn('job_id', array(
            'header' => Mage::helper('career')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'job_id',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('career')->__('Job Title'),
            'align' => 'left',
            'index' => 'title',
            'width' => '100px'
        ));

        $this->addColumn('function', array(
            'header' => Mage::helper('career')->__('Function'),
            'align' => 'left',
            'index' => 'function',
            'width' => '100px'
        ));

        $this->addColumn('position', array(
            'header' => Mage::helper('career')->__('Position'),
            'align' => 'left',
            'index' => 'position',
            'width' => '100px'
        ));

        $this->addColumn('location', array(
            'header' => Mage::helper('career')->__('Location'),
            'align' => 'left',
            'index' => 'location',
            'width' => '100px'
        ));

        $this->addColumn('scope_of_work', array(
            'header' => Mage::helper('career')->__('Scope Of Work'),
            'align' => 'left',
            'index' => 'scope_of_work',
            'width' => '100px'
        ));

        $this->addColumn('qualifications', array(
            'header' => Mage::helper('career')->__('Qualifications'),
            'align' => 'left',
            'index' => 'qualifications',
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
        $this->setMassactionIdField('job_id');
        $this->getMassactionBlock()->setFormFieldName('job');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('career')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('career')->__('Are you sure?')
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
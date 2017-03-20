<?php
class Magebuzz_Career_Block_Adminhtml_Application_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    public function __construct() {
        parent::__construct();
        $this->setId('application');
        $this->setDefaultSort('application_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {

        $collection = Mage::getModel('career/application')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns() {
        $this->addColumn('application_id', array(
            'header' => Mage::helper('career')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'application_id',
        ));

        $this->addColumn('identity_number', array(
            'header' => Mage::helper('career')->__('Identity Number'),
            'align' => 'left',
            'index' => 'identity_number',
            'width' => '100px'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('career')->__('Email'),
            'align' => 'left',
            'index' => 'email',
            'width' => '100px'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('career')->__('Name'),
            'align' => 'left',
            'index' => 'name',
            'width' => '100px'
        ));

        $this->addColumn('tel', array(
            'header' => Mage::helper('career')->__('Telephone'),
            'align' => 'left',
            'index' => 'tel',
            'width' => '100px'
        ));

        $this->addColumn('work_type', array(
            'header' => Mage::helper('career')->__('Work Type'),
            'align' => 'left',
            'index' => 'work_type',
            'width' => '100px'
        ));

        $this->addColumn('work_age', array(
            'header' => Mage::helper('career')->__('Work Age'),
            'align' => 'left',
            'index' => 'work_age',
            'width' => '100px'
        ));

        $this->addColumn('present_company', array(
            'header' => Mage::helper('career')->__('Present Company'),
            'align' => 'left',
            'index' => 'present_company',
            'width' => '100px'
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
        $this->setMassactionIdField('application_id');
        $this->getMassactionBlock()->setFormFieldName('application');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('career')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('career')->__('Are you sure?')
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array(
                'store'=>$this->getRequest()->getParam('store'),
                'id'=>$row->getId())
        );
    }
}
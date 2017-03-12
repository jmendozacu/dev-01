<?php
class Magebuzz_Franchise_Block_Adminhtml_Franchise_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    public function __construct() {
        parent::__construct();
        $this->setId('franchise');
        $this->setDefaultSort('franchise_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {

        $collection = Mage::getModel('franchise/franchise')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns() {
        $this->addColumn('franchise_id', array(
            'header' => Mage::helper('franchise')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'franchise_id',
        ));

        $this->addColumn('contact_name', array(
            'header' => Mage::helper('franchise')->__('Contact Name'),
            'align' => 'left',
            'index' => 'contact_name',
            'width' => '100px'
        ));

        $this->addColumn('company_name', array(
            'header' => Mage::helper('franchise')->__('Company Name'),
            'align' => 'left',
            'index' => 'company_name',
            'width' => '100px'
        ));

        $this->addColumn('address', array(
            'header' => Mage::helper('franchise')->__('Address'),
            'align' => 'left',
            'index' => 'address',
            'width' => '100px'
        ));

        $this->addColumn('country', array(
            'header' => Mage::helper('franchise')->__('Country'),
            'align' => 'left',
            'index' => 'country',
            'width' => '100px'
        ));

        $this->addColumn('tel', array(
            'header' => Mage::helper('franchise')->__('Telephone'),
            'align' => 'left',
            'index' => 'tel',
            'width' => '100px'
        ));

        $this->addColumn('fax', array(
            'header' => Mage::helper('franchise')->__('Fax'),
            'align' => 'left',
            'index' => 'fax',
            'width' => '100px'
        ));

        $this->addColumn('website', array(
            'header' => Mage::helper('franchise')->__('Website'),
            'align' => 'left',
            'index' => 'website',
            'width' => '100px'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('franchise')->__('Email'),
            'align' => 'left',
            'index' => 'email',
            'width' => '100px'
        ));

        $this->addColumn('current_business', array(
            'header' => Mage::helper('franchise')->__('Current Business'),
            'align' => 'left',
            'index' => 'current_business',
            'width' => '100px'
        ));

        $this->addColumn('retail_business', array(
            'header' => Mage::helper('franchise')->__('Retail Business'),
            'align' => 'left',
            'index' => 'retail_business',
            'width' => '100px'
        ));

        $this->addColumn('number_staff', array(
            'header' => Mage::helper('franchise')->__('Number Staff'),
            'align' => 'left',
            'index' => 'number_staff',
            'width' => '100px'
        ));

        $this->addColumn('sale_turnover', array(
            'header' => Mage::helper('franchise')->__('Sale Turnover'),
            'align' => 'left',
            'index' => 'sale_turnover',
            'width' => '100px'
        ));

        $this->addColumn('warehouse', array(
            'header' => Mage::helper('franchise')->__('Warehouse'),
            'align' => 'left',
            'index' => 'warehouse',
            'width' => '100px'
        ));

        $this->addColumn('experience', array(
            'header' => Mage::helper('franchise')->__('Experience'),
            'align' => 'left',
            'index' => 'experience',
            'width' => '100px'
        ));

        $this->addColumn('potential_competitor', array(
            'header' => Mage::helper('franchise')->__('Potential Competitor'),
            'align' => 'left',
            'index' => 'potential_competitor',
            'width' => '100px'
        ));

        $this->addColumn('potential_locations', array(
            'header' => Mage::helper('franchise')->__('Potential Locations'),
            'align' => 'left',
            'index' => 'potential_locations',
            'width' => '100px'
        ));


        $this->addColumn('action', array(
            'header' => Mage::helper('franchise')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('franchise')->__('Delete'),
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
        $this->setMassactionIdField('franchise_id');
        $this->getMassactionBlock()->setFormFieldName('franchise');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('franchise')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('franchise')->__('Are you sure?')
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
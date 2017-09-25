<?php

class MarginFrame_Dataflow_Block_Adminhtml_Profile_Import_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    
    /**
    * Init Grid default properties
    *
    */
    public function __construct()
    {
        parent::__construct();
        $this->setId('import_profile_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
    }
    
    /**
    * @return MarginFrame_Dataflow_Block_Adminhtml_Profile_Import_Grid
    */
    protected function _prepareCollection()
    {
        /** @var $collection MarginFrame_Dataflow_Model_Resource_Profile_Import_Collection */
        $collection = Mage::getResourceModel('marginframe_dataflow/profile_import_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function getColumnRenderers()
    {
        return array(
            'actions'  => 'marginframe_dataflow/adminhtml_profile_import_grid_renderer_actions',
        );
    }
    
    /**
    * @return MarginFrame_Dataflow_Block_Adminhtml_Profile_Import_Grid
    */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'        => $this->__('ID'),
            'index'         => 'entity_id',
            'header_export' => 'entity_id',
            'width'         => '50px',
        ));

        $this->addColumn('name', array(
            'header'        => $this->__('Name'),
            'index'         => 'name',
            'header_export' => 'name',
        ));

        $this->addColumn('actions', array(
            'header'        => $this->__('Actions'),
            'sortable'      => false,
            'filter'        => false,
            'width'         => '100px',
            'type'          => 'actions',
        ));

        return parent::_prepareColumns();
    }
    
    /**
    * @param Mage_Core_Model_Abstract $item
    * @return string
    */
    public function getRowUrl($item)
    {
        return $this->getUrl('*/*/edit', array('id' => $item->getId()));
    }
}

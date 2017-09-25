<?php

class MarginFrame_Dataflow_Model_Import_Adapter_Type_Product
    extends MarginFrame_Dataflow_Model_Import_Adapter_Abstract
{
    /** @var  array */
    protected $_dataProcessors;
    /** @var MarginFrame_Dataflow_Helper_Data */
    protected $_helper;
    /** @var MarginFrame_Dataflow_Helper_Reflection */
    protected $_reflectionHelper;
    /** @var Varien_Db_Adapter_Interface */
    protected $_resourceConnection;
    /** @var string */
    protected $_productTable;
    /** @var array */
    protected $_skuToId;

    /**
     * @return Mage_Catalog_Model_Resource_Product
     */
    protected function _getEntityTypeId()
    {
        /** @var $resource Mage_Catalog_Model_Resource_Product */
        $resource = Mage::getResourceSingleton('catalog/product');

        return $resource->getTypeId();
    }

    /**
     * Initialization
     *
     * @return $this
     */
    protected function _construct()
    {
        $this->_helper             = Mage::helper('marginframe_dataflow');
        $this->_reflectionHelper   = Mage::helper('marginframe_dataflow/reflection');
        $this->_resourceConnection = $this->_config->getResourceConnection();
        $this->_productTable       = $this->_config->getResource()->getTableName('catalog/product');

        $this->_dataProcessors = array(
            0 => new MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Base($this),
            1 => new MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Category($this),
            2 => new MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Eav($this),
            3 => new MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Inventory($this),
            4 => new MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Media($this),
            5 => new MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Link($this),
            6 => new MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Price($this),
            7 => new MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Options($this),
            8 => new MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Configurable($this),
            9 => new MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Grouped($this),
        );

        return $this;
    }

    /**
     * Calls before prepare begin
     *
     * @return $this
     */
    public function beforePrepare()
    {
        foreach ($this->_dataProcessors as $processor) {
            /** @var $processor MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Abstract */
            $processor->beforePrepare();
        }

        return $this;
    }

    /**
     * Prepare data for row
     *
     * @param array $data
     * @return MarginFrame_Dataflow_Model_Import_Adapter_Interface
     */
    public function prepareData(array &$data)
    {
        foreach ($this->_dataProcessors as $processor) {
            /** @var $processor MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Abstract */
            $processor->prepareData($data);
        }

        return $this;
    }

    /**
     * @return MarginFrame_Dataflow_Model_Import_Adapter_Interface
     */
    public function afterPrepare()
    {
        foreach ($this->_dataProcessors as $processor) {
            /** @var $processor MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Abstract */
            $processor->afterPrepare();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function beforeProcess()
    {
        foreach ($this->_dataProcessors as $processor) {
            /** @var $processor MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Abstract */
            $processor->beforeProcess();
        }
        $this->_skuToId = $this->_resourceConnection->fetchPairs(
            $this->_resourceConnection->select()->from($this->_productTable, array('sku', 'entity_id'))
        );

        return $this;
    }

    /**
     * @param array $data
     * @return array
     * @throws MarginFrame_Dataflow_Exception
     */
    protected function _prepareAndValidateData(array &$data)
    {
        if (!isset($data['sku']) || !$data['sku'] || !trim($data['sku'])) {
            $this->_throwException($this->_helper->__('Product SKU is empty or not found.'));
        }
        $data['sku'] = trim($data['sku']);

        if (isset($data['type'])) {
            $data['type_id'] = $data['type'];
            unset($data['type']);
        }

        return $data;
    }

    /**
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function processData(array &$data)
    {
        $this->_prepareAndValidateData($data);

        if (isset($this->_skuToId[$data['sku']])) {
            $data['product_id']   = $this->_skuToId[$data['sku']];
        } else if (!$this->_config->getCanCreateNewEntity()) {
            return false;
        }

        if ($beforeProcessCallback = $this->getConfig()->getBeforeProcessCallback()) {
            $this->_reflectionHelper->getReflation($beforeProcessCallback)
                ->invokeArgs(null, array('data' => &$data));
        }
        $this->_resourceConnection->beginTransaction();
        try {
            foreach ($this->_dataProcessors as $processor) {
                /** @var $processor MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Abstract */
                $processor->processData($data);
            }
            $this->_skuToId[$data['sku']] = $data['product_id'];
            $this->_resourceConnection->commit();
        } catch (Exception $e) {
            $this->_resourceConnection->rollBack();
            throw $e;
        }

        return true;
    }

    /**
     * @return $this
     */
    public function afterProcess()
    {
        foreach ($this->_dataProcessors as $processor) {
            /** @var $processor MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Abstract */
            $processor->afterProcess($this->_skuToId);
        }

        return $this;
    }
}

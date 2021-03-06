<?php

abstract class MarginFrame_Dataflow_Model_Import_Adapter_Type_Product_Data_Abstract
{
    const  DELETE_VALUE_FLAG = 'unset';
    /** @var  MarginFrame_Dataflow_Model_Import_Adapter_Type_Product */
    protected $_adapter;
    /** @var  MarginFrame_Dataflow_Model_Import_Adapter_Config */
    protected $_config;
    /** @var  MarginFrame_Dataflow_Helper_Data */
    protected $_helper;
    /** @var  MarginFrame_Dataflow_Helper_Reflection */
    protected $_reflectionHelper;
    /** @var  Mage_Core_Model_Resource */
    protected $_resource;
    /** @var  Varien_Db_Adapter_Pdo_Mysql */
    protected $_readConnection;
    /** @var  Varien_Db_Adapter_Pdo_Mysql */
    protected $_writeConnection;
    /** @var  Mage_Core_Model_Website */
    protected $_website;
    /** @var  Mage_Core_Model_Store */
    protected $_store;


    /**
     * @param MarginFrame_Dataflow_Model_Import_Adapter_Type_Product $adapter
     */
    final public function __construct(MarginFrame_Dataflow_Model_Import_Adapter_Type_Product $adapter)
    {
        $this->_adapter          = $adapter;
        $this->_config           = $adapter->getConfig();
        $this->_resource         = $this->_config->getResource();
        $this->_helper           = Mage::helper('marginframe_dataflow');
        $this->_reflectionHelper = Mage::helper('marginframe_dataflow/reflection');
        $this->_website          = $this->_config->getWebsite();
        $this->_store            = $this->_config->getStore();

        $this->_readConnection   = $this->_config->getResourceConnection();
        $this->_writeConnection  = $this->_config->getResourceConnection();
        $this->_construct();
    }

    /**
     * @return $this
     */
    protected function _construct()
    {
        return $this;
    }

    /**
     * @param string $message
     * @param int $code
     * @param Exception $previous
     * @throws MarginFrame_Dataflow_Model_Import_Adapter_Exception
     */
    protected function _throwException($message, $code = 0, Exception $previous = null)
    {
        throw new MarginFrame_Dataflow_Model_Import_Adapter_Exception($message, $code, $previous);
    }

    /**
     * Will be called before preparing
     *
     * @return $this
     */
    public function beforePrepare()
    {
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function prepareData(array &$data)
    {
        return $this;
    }

    /**
     * Will be called when all rows prepared
     *
     * @return $this
     */
    public function afterPrepare()
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function beforeProcess()
    {
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    abstract public function processData(array &$data);

    /**
     * @param $skuToId
     * @return $this
     */
    public function afterProcess($skuToId)
    {
        return $this;
    }
}
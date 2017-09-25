<?php

class MarginFrame_Dataflow_Model_Import_Operator_Config
{
    /** @var MarginFrame_Dataflow_Model_Import_Parser_Interface */
    protected $_parser;
    /** @var MarginFrame_Dataflow_Model_Import_Adapter_Interface */
    protected $_adapter;
    /** @var MarginFrame_Dataflow_Model_Storage_Interface */
    protected $_tmpStorage;

    /**
     * @param MarginFrame_Dataflow_Model_Import_Parser_Interface $parser
     * @param MarginFrame_Dataflow_Model_Import_Adapter_Interface $adapter
     * @param MarginFrame_Dataflow_Model_Storage_Interface $tmpStorage
     */
    public function __construct(
        MarginFrame_Dataflow_Model_Import_Parser_Interface $parser,
        MarginFrame_Dataflow_Model_Import_Adapter_Interface $adapter,
        MarginFrame_Dataflow_Model_Storage_Interface $tmpStorage
    )
    {
        $this->_parser     = $parser;
        $this->_adapter    = $adapter;
        $this->_tmpStorage = $tmpStorage;
    }

    /**
     * @return MarginFrame_Dataflow_Model_Import_Parser_Interface
     */
    public function getParser()
    {
        return $this->_parser;
    }

    /**
     * @return MarginFrame_Dataflow_Model_Import_Adapter_Interface
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * @return MarginFrame_Dataflow_Model_Storage_Interface
     */
    public function getTemporaryStorage()
    {
        return $this->_tmpStorage;
    }
}

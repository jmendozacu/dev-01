<?php

abstract class MarginFrame_Dataflow_Model_Import_Adapter_Abstract
    implements MarginFrame_Dataflow_Model_Import_Adapter_Interface
{
    /** @var MarginFrame_Dataflow_Model_Import_Adapter_Config */
    protected $_config;

    final public function __construct(MarginFrame_Dataflow_Model_Import_Adapter_Config $config)
    {
        $config->setEntityTypeId($this->_getEntityTypeId());
        $this->_config = $config;
        $this->_construct();
    }

    /**
     * @return MarginFrame_Dataflow_Model_Import_Adapter_Config
     */
    final public function getConfig()
    {
        return $this->_config;
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
     * @return $this
     */
    abstract protected function _construct();

    /**
     * @return int
     */
    abstract protected function _getEntityTypeId();
}

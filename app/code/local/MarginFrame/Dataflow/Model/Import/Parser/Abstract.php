<?php

abstract class MarginFrame_Dataflow_Model_Import_Parser_Abstract
    implements MarginFrame_Dataflow_Model_Import_Parser_Interface
{
    /**
     * @param string $message
     * @param int $code
     * @throws MarginFrame_Dataflow_Model_Import_Parser_Exception
     */
    protected function _throwException($message, $code = 0)
    {
        throw new MarginFrame_Dataflow_Model_Import_Parser_Exception($message, $code);
    }
}
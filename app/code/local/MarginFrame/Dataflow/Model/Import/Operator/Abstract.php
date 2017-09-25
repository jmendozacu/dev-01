<?php

abstract class MarginFrame_Dataflow_Model_Import_Operator_Abstract
{
    /**
     * @param string $message
     * @param int $code
     * @param Exception $previous
     * @throws MarginFrame_Dataflow_Exception
     */
    protected function _throwException($message, $code = 0, Exception $previous = null)
    {
        throw new MarginFrame_Dataflow_Exception($message, $code, $previous);
    }
}
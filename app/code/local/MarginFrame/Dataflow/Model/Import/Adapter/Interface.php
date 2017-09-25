<?php

interface MarginFrame_Dataflow_Model_Import_Adapter_Interface
{
    public function beforePrepare();
    public function prepareData(array &$data);
    public function afterPrepare();
    public function beforeProcess();
    public function processData(array &$data);
    public function afterProcess();
}

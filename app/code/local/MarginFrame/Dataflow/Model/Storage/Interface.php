<?php

interface MarginFrame_Dataflow_Model_Storage_Interface
{
    public function saveData(array &$data);
    public function getData();
    public function rewind();
}

<?php

class MarginFrame_Dataflow_Model_Storage_File
    implements MarginFrame_Dataflow_Model_Storage_Interface
{
    /** @var resource */
    protected $_resource;

    /**
     * Class constructor
     */
    public function __construct($tmpFilePath)
    {
        $this->_resource = fopen($tmpFilePath, 'wb+');
		chmod($tmpFilePath, 0755);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function saveData(array &$data)
    {
        fwrite($this->_resource, json_encode($data) . PHP_EOL);
        return $this;
    }

    /**
     * @return bool|array
     */
    public function getData()
    {
        if ($string = fgets($this->_resource)) {
            return json_decode($string, true);
        }
        return false;
    }

    /**
     * @return $this
     */
    public function rewind()
    {
        rewind($this->_resource);
        return $this;
    }
}

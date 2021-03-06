<?php

class MarginFrame_Dataflow_Model_Import_Parser_Config_Csv
{
    /** @var  string */
    protected $_dataFilePath;
    /** @var  array */
    protected $_columnMapping;
    /** @var  string */
    protected $_delimiter = ',';
    /** @var  string */
    protected $_enclosure = '"';
    /** @var  string */
    protected $_escape    = '\\';

    /**
     * @param string $dataFilePath
     */
    public function __construct($dataFilePath)
    {
        $this->_dataFilePath = $dataFilePath;
    }

    /**
     * @return string
     */
    public function getDataFilePath()
    {
        return $this->_dataFilePath;
    }

    /**
     * @param array $mapping
     * @return $this
     */
    public function setColumnMapping(array $mapping)
    {
        $this->_columnMapping = $mapping;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getColumnMapping()
    {
        return $this->_columnMapping;
    }

    /**
     * @param string $delimiter
     * @return $this
     */
    public function setDelimiter($delimiter)
    {
        if ($delimiter == '\t') {
            $delimiter = "\t";
        }
        $this->_delimiter = (string) $delimiter;
        return $this;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->_delimiter;
    }

    /**
     * @param $enclosure
     * @return $this
     */
    public function setEnclosure($enclosure)
    {
        $this->_enclosure = (string) $enclosure;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnclosure()
    {
        return $this->_enclosure;
    }

    /**
     * @param $escape
     * @return $this
     */
    public function setEscape($escape)
    {
        $this->_escape = (string) $escape;
        return $this;
    }

    /**
     * @return string
     */
    public function getEscape()
    {
        return $this->_escape;
    }
}

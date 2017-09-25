<?php

class MarginFrame_Dataflow_Model_Import_Parser_Csv
    extends MarginFrame_Dataflow_Model_Import_Parser_Abstract
{
    protected $_file;
    protected $_firstRowPosition;
    protected $_primaryColumn;
    protected $_primaryColumnIndex = array();
    protected $_columnKeyToId = array();
    protected $_columnIdToKey = array();

    // CSV format configuration
    protected $_delimiter;
    protected $_enclosure;
    protected $_escape;

    /**
     * @return MarginFrame_Dataflow_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('marginframe_dataflow');
    }

    /**
     * @param MarginFrame_Dataflow_Model_Import_Parser_Config_Csv $config
     * @throws MarginFrame_Dataflow_Model_Import_Parser_Exception
     */
    public function __construct(MarginFrame_Dataflow_Model_Import_Parser_Config_Csv $config)
    {
        ini_set("auto_detect_line_endings", true);

        $dataFilePath     = $config->getDataFilePath();
        $columnMapping    = $config->getColumnMapping();
        $primaryKey       = false;

        if (!is_file($dataFilePath) || !is_readable($dataFilePath)) {
            $this->_throwException($this->getHelper()->__('CSV file not found or not readable: %s', $dataFilePath));
        }

        $this->_file      = fopen($dataFilePath, 'r');
        $this->_delimiter = $config->getDelimiter();
        $this->_enclosure = $config->getEnclosure();
        $this->_escape    = $config->getEscape();

        if (is_array($columnMapping)) {
            $this->_setColumnMapping($columnMapping);
        } else {
            $this->_prepareHeaders();
        }
		
		//ipune: force to add url_key column
        if(!isset($this->_columnKeyToId['url_key'])) {
			$this->_columnKeyToId['url_key'] = 9999;
			$this->_columnIdToKey[9999] = 'url_key';
        }
        //ipune: add static options_container for configuration product
        if(!isset($this->_columnKeyToId['options_container'])) {
        	$this->_columnKeyToId['options_container'] = 9998;
        	$this->_columnIdToKey[9998] = 'options_container';
        }
        
        $this->_firstRowPosition = ftell($this->_file);
        if ($primaryKey) {
            $this->_prepareIndexMap($primaryKey);
        }
    }

    /**
     * Check is correct data structure
     *
     * @throws MarginFrame_Dataflow_Model_Import_Parser_Exception
     */
    public function validate()
    {
        if (empty($this->_columnIdToKey) || empty($this->_columnKeyToId)) {
            $this->_throwException(Mage::helper('marginframe_dataflow')->__("Can't find column headers."));
        }
    }

    /**
     * Read next row from csv file
     *
     * @param bool $hash
     * @return array|bool
     */
    public function getData($hash = true)
    {
        //ipune add setlocale
		setlocale(LC_ALL, 'en_US.UTF-8');
		if (false !== ($data = fgetcsv($this->_file, null, $this->_delimiter, $this->_enclosure))) {
        //if (false !== ($data = fgetcsv($this->_file, null, $this->_delimiter, $this->_enclosure, $this->_escape))) { // Php 5.2.x not supported escape arg
            if ($hash) {
                return $this->_getRowHash($data);
            } else {
                return $data;
            }
            //Mage::log($data, null, 'mgf_import.log');
        }
        return false;
    }

    /**
     * Select row by primary column value
     *
     * @param string $primaryColumnValue
     * @return array|null
     */
    public function getRow($primaryColumnValue)
    {
        //ipune add setlocale
		setlocale(LC_ALL, 'en_US.UTF-8');
		if (isset($this->_primaryColumnIndex[$primaryColumnValue])) {
            fseek($this->_file, $this->_primaryColumnIndex[$primaryColumnValue]);
            $result = $this->_getRowHash(
                fgetcsv($this->_file, null, $this->_delimiter, $this->_enclosure));
                //fgetcsv($this->_file, null, $this->_delimiter, $this->_enclosure, $this->_escape)); // Php 5.2.x not supported escape arg
            if (!empty($result)) {
                return $result;
            }
        }
        return null;
    }

    /**
     * Return to first data row
     *
     * @return MarginFrame_Dataflow_Model_Import_Parser_Csv
     */
    public function rewind()
    {
        fseek($this->_file, $this->_firstRowPosition);
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributeCodes()
    {
        return $this->_columnIdToKey;
    }

    /**
     * Get cell value for column by column name
     *
     * @param string $columnName
     * @param array $row
     * @return string|null
     */
    protected function _getColumnValue($columnName, $row)
    {
        if (isset($this->_columnKeyToId[$columnName], $row[$this->_columnKeyToId[$columnName]])) {
            return $row[$this->_columnKeyToId[$columnName]];
        }
        return null;
    }

    /**
     * @param array $map
     * @return $this
     */
    protected function _setColumnMapping(array $map)
    {
        foreach ($map as $id => $key)
        {
            $this->_columnKeyToId[$key] = $id;
            $this->_columnIdToKey[$id] = $key;
        }
        return $this;
    }

    /**
     * Create headers map
     *
     * @return MarginFrame_Dataflow_Model_Import_Parser_Csv
     */
    protected function _prepareHeaders()
    {
		//ipune add setlocale
		setlocale(LC_ALL, 'en_US.UTF-8');
		return $this->_setColumnMapping(
            fgetcsv($this->_file, null, $this->_delimiter, $this->_enclosure));
            //fgetcsv($this->_file, null, $this->_delimiter, $this->_enclosure, $this->_escape)); // Php 5.2.x not supported escape arg
    }

    /**
     * Create hash map for primary column
     *
     * @param $primaryKey
     * @return MarginFrame_Dataflow_Model_Import_Parser_Csv
     */
    protected function _prepareIndexMap($primaryKey)
    {
        //ipune add setlocale
		setlocale(LC_ALL, 'en_US.UTF-8');
		$this->_primaryColumn = $primaryKey;
        $current = ftell($this->_file);
        //while (false !== ($row = fgetcsv($this->_file, null, $this->_delimiter, $this->_enclosure, $this->_escape))) { // Php 5.2.x not supported escape arg
        while (false !== ($row = fgetcsv($this->_file, null, $this->_delimiter, $this->_enclosure))) {
            $value = $this->_getColumnValue($this->_primaryColumn, $row);
            if ($value) {
                $this->_primaryColumnIndex[$value] = $current;
            }
            $current = ftell($this->_file);
        }
        $this->rewind();
        return $this;
    }

    /**
     * Convert array to hash, when use headers for keys
     *
     * @param array $rowData
     * @return array
     */
    protected function _getRowHash($rowData)
    {
        $result = array();
        if (is_array($rowData) && !empty($rowData)) {
            foreach ($rowData as $id => $value) {
                if (isset($this->_columnIdToKey[$id])) {
                    $result[$this->_columnIdToKey[$id]] = $value;
                }
            }
        }
        return $result;
    }

    /**
     * Close file when destroy object
     */
    public function __destruct()
    {
        fclose($this->_file);
    }
}

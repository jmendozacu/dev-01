<?php

class MarginFrame_Dataflow_Model_Profile_Import
    extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'marginframe_dataflow_profile_import';

    /**
     * @return bool
     */
    public function useCustomMapping()
    {
        return (bool) $this->getData('custom_column_mapping');
    }

    /**
     * Class constructor
     */
    protected function _construct()
    {
        $this->_init('marginframe_dataflow/profile_import');
    }

    /**
     * @return MarginFrame_Dataflow_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('marginframe_dataflow');
    }

    /**
     * @return array
     */
    protected function _getTmpFilePath($path = null)
    {
        if(is_null($path) || empty($path)) {
			return Mage::getBaseDir('tmp') . DS . hash('crc32', uniqid()) . '.csv';
		} else
		{
			//return $path;
			return $path . DS . hash('crc32', uniqid()) . '.csv';
		}
    }

    /**
     * @return string
     * @throws MarginFrame_Dataflow_Exception
     */
    public function getAdapterClassName()
    {
        $adapterClassName = Mage::getConfig()->getModelClassName($this->getData('adapter_model'));
        if (!class_exists($adapterClassName)) {
            throw new MarginFrame_Dataflow_Exception(
                $this->getHelper()->__('Wrong adapter model "%s"', $adapterClassName)
            );
        }

        return $adapterClassName;
    }

    /**
     * @param $dataFilePath
     * @return MarginFrame_Dataflow_Model_Import_Parser_Csv
     */
    protected function _initializeParser($dataFilePath)
    {
        $parserConfig = new MarginFrame_Dataflow_Model_Import_Parser_Config_Csv($dataFilePath);
        if ($this->useCustomMapping()) {
            // Use custom column mapping
            $parserConfig->setColumnMapping($this->getMapping());
        }

        $parserConfig->setDelimiter($this->getData('column_delimiter'));

        $parser = new MarginFrame_Dataflow_Model_Import_Parser_Csv($parserConfig);

        return $parser;
    }

    /**
     * @param MarginFrame_Dataflow_Model_Import_Parser_Interface $parser
     */
    protected function _initializeAdapter(MarginFrame_Dataflow_Model_Import_Parser_Interface $parser, $imagePath = null)
    {
        if ($this->useCustomMapping()) {
            // Get user defined attribute codes
            $attributeCodes = $this->getMapping();
        } else {
            // Retrieve attribute codes from column headers.
            $attributeCodes = $parser->getAttributeCodes();
        }
        // Initialize data adapter
        $adapterClassName = $this->getAdapterClassName();
        $adapterConfig    = new MarginFrame_Dataflow_Model_Import_Adapter_Config($attributeCodes);

        $adapterConfig->setStoreId($this->getData('scope'));
        $adapterConfig->setCanCreateUrlkey($this->getData('can_create_urlkey'));
        $adapterConfig->setCanCreateNewEntity($this->getData('can_create_new_entity'));
        $adapterConfig->setCanCreateOptions($this->getData('can_create_options'));
        $adapterConfig->setCanCreateCategories($this->getData('can_create_categories'));
        $adapterConfig->setCanDownloadMedia($this->getData('can_download_media'));
        $adapterConfig->setOptionCorrectionFactor($this->getData('option_correction_percent'));
        $adapterConfig->setOptionDelimiter($this->getData('option_delimiter'));
        $adapterConfig->setOptionImagePath($imagePath);

        return new $adapterClassName($adapterConfig);
    }

    /**
     * Execute profile
     *
     * @param string $dataFilePath
     * @return Varien_Object
     * @throws Exception
     */
    //@TODO: ipune
    public function run($dataFilePath, $imagePath = null, $tmpPath = null)
    {
		$tmpStorage = new MarginFrame_Dataflow_Model_Storage_File($this->_getTmpFilePath($tmpPath));
        $parser     = $this->_initializeParser($dataFilePath);
        $adapter    = $this->_initializeAdapter($parser, $imagePath);

        $operatorConfig   = new MarginFrame_Dataflow_Model_Import_Operator_Config($parser, $adapter, $tmpStorage);
        $operator = new MarginFrame_Dataflow_Model_Import_Operator($operatorConfig);
        $operator->run();
    }

    /**
     * @return Mage_Core_Model_Abstract|void
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (is_array($this->getData('mapping'))) {
            $this->setData('mapping', $this->getHelper()->jsonEncode($this->getData('mapping')));
        }
        if (is_array($this->getData('schedule_config'))) {
            $this->setData('schedule_config', Mage::helper('core')->jsonEncode($this->getData('schedule_config')));
        }
        if (0 != $this->getData('scope')) {
            $this->setData('can_create_new_entity', 0);
            $this->setData('can_create_urlkey', 0);
            $this->setData('can_create_options', 0);
        }
    }

    /**
     * @return array|bool
     */
    public function getMapping()
    {
        $mapping = $this->getData('mapping');
        if (!is_array($mapping)) {
            $mapping = $this->getHelper()->jsonDecode($mapping);
        }

        return $mapping;
    }

    /**
     * @return Varien_Object
     */
    public function getScheduleConfig()
    {
        $config = $this->getData('schedule_config');
        if (!is_array($config)) {
            $config = $this->getHelper()->jsonDecode($config);
        }

        return new Varien_Object($config);
    }

    /**
     * Checks the observer's cron expression against time
     *
     * Supports $this->setCronExpr('* 0-5,10-59/5 2-10,15-25 january-june/2 mon-fri')
     *
     * @param  $time
     * @param  $getCronExpr
     * @return bool
     */
    public function trySchedule($time, $getCronExpr)
    {
        $coreModel = new Mage_Cron_Model_Schedule();

        if (!$getCronExpr || !$time) {
            return false;
        }
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }

        $d = getdate(Mage::getSingleton('core/date')->timestamp($time));

        $match = $coreModel->matchCronExpression($getCronExpr[0], $d['minutes'])
            && $coreModel->matchCronExpression($getCronExpr[1], $d['hours'])
            && $coreModel->matchCronExpression($getCronExpr[2], $d['mday'])
            && $coreModel->matchCronExpression($getCronExpr[3], $d['mon'])
            && $coreModel->matchCronExpression($getCronExpr[4], $d['wday']);

        return $match;
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Model_Product_Image extends Mage_Catalog_Model_Product_Image
{
	protected $_suffix;

	public function setSuffix($suffix)
	{
		$this->_suffix = $suffix;

		return $this;
	}

	public function getSuffix()
	{
		if($this->_suffix) {
			return $this->_suffix;
		} else {
			return '-'.$this->getWidth().'__'.$this->getHeight();
		}
	}

	public function setBaseFile($file)
	{
		$this->_isBaseFilePlaceholder = false;

		if (!$this->_checkMemory($file)) {
			$file = null;
		}

		if ((!$file) || (!file_exists($file))) {
			throw new Exception(Mage::helper('catalog')->__('Image file was not found.'));
		}

		$this->_baseFile = $file;

		$info = pathinfo($file);
		$file_name = basename($file,'.'.$info['extension']).$this->getSuffix().'.'.$info['extension'];

		$this->_newFile = dirname($file).DS.$file_name;

		return $this;
	}

    public function setSize($size)
    {
        // determine width and height from string
        list($width, $height) = explode('__', strtolower($size), 2);
        foreach (array('width', 'height') as $wh) {
            $$wh  = (int)$$wh;
            if (empty($$wh))
                $$wh = null;
        }

        // set sizes
        $this->setWidth($width)->setHeight($height);

        return $this;
    }
}

<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

class Amasty_Meta_Block_Page_Html_Head extends Mage_Page_Block_Html_Head
{
	protected $_rewrittenData = array();

	/**
	 * Set title element text
	 *
	 * @param string $value
	 * @param bool $isMain
	 * @param bool $force
	 *
	 * @return $this|Mage_Page_Block_Html_Head
	 */
	public function setTitle($value, $isMain = false, $force = false)
	{
        $replaced = Mage::registry('ammeta_replaced_data');
        if (!isset($replaced['meta_title']) || $replaced['meta_title'] != $value) {
            $value = Mage::getStoreConfig('design/head/title_prefix') . ' ' . $value
                . ' ' . Mage::getStoreConfig('design/head/title_suffix');
        }
		$this->_rewriteData('title', $value, $isMain, $force);

		return $this;
	}

	/**
	 * @param array|string $key
	 * @param mixed|null $value
	 * @param bool $isMain
	 *
	 * @return $this|Varien_Object
	 */
	public function setData($key, $value = false, $isMain = false, $force = false)
	{
		$this->_rewriteData($key, $value, $isMain, $force);

		return $this;
	}

	/**
	 * @param $dataKey
	 * @param $value
	 * @param $isMain
	 * @param $force
	 *
	 * @return $this|Varien_Object
	 */
	protected function _rewriteData($dataKey, $value, $isMain, $force = false)
	{
		if (! in_array($dataKey, $this->_rewrittenData) || $force) {
			if ($isMain) {
				$this->_rewrittenData[] = $dataKey;
			}

			return parent::setData($dataKey, $value);
		}

		return $this;
	}

	/**
	 * @param string $method
	 * @param array $args
	 *
	 * @return $this|mixed|Varien_Object
	 * @throws Varien_Exception
	 */
	public function __call($method, $args)
	{
		switch (substr($method, 0, 3)) {
			case 'set' :
				$key    = $this->_underscore(substr($method, 3));
				$result = $this->setData($key, isset($args[0]) ? $args[0] : null, isset($args[1]) ? $args[1] : false, isset($args[2]) ? $args[2] : false);

				return $result;
			default :
				return parent::__call($method, $args);
		}
	}

	public function addExternalItem($type, $name, $params = null, $if = null, $cond = null) {
    parent::addItem($type, $name, $params = null, $if = null, $cond = null);
  }

  public function removeExternalItem($type, $name) {
    parent::removeItem($type, $name);
  }

  protected function _separateOtherHtmlHeadElements(&$lines, $itemIf, $itemType, $itemParams, $itemName, $itemThe) {
    $params = $itemParams ? ' ' . $itemParams : '';
    $href = $itemName;
    switch ($itemType) {
      case 'rss':
        $lines[$itemIf]['other'][] = sprintf('<link href="%s"%s rel="alternate" type="application/rss+xml" />', $href, $params);
        break;
      case 'link_rel':
        $lines[$itemIf]['other'][] = sprintf('<link%s href="%s" />', $params, $href);
        break;

      case 'external_js':
        $lines[$itemIf]['other'][] = sprintf('<script type="text/javascript" src="%s" %s></script>', $href, $params);
        break;

      case 'external_css':
        $lines[$itemIf]['other'][] = sprintf('<link rel="stylesheet" type="text/css" href="%s" %s/>', $href, $params);
        break;
    }
  }
}

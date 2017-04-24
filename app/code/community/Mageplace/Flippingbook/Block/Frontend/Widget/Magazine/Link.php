<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Frontend_Widget_Magazine_Link
	extends Mage_Core_Block_Html_Link
	implements Mage_Widget_Block_Interface
{
	/**
	 * Prepared href attribute
	 *
	 * @var string
	 */
	protected $_href;

	/**
	 * Prepared onclick attribute
	 *
	 * @var string
	 */
	protected $_onclick;

	/**
	 * Prepared title attribute
	 *
	 * @var string
	 */
	protected $_title;

	/**
	 * Prepared anchor text
	 *
	 * @var string
	 */
	protected $_anchorText;

	/**
	 * Prepared thumb url
	 *
	 * @var string
	 */
	protected $_thumbUrl;

	/**
	 * Prepared magazine object
	 *
	 * @var Mageplace_Flippingbook_Model_Magazine
	 */
	protected $_magazine;



	/**
	 * Prepare flippingbook url.
	 *
	 * @return string
	 */
	public function getHref()
	{
		if(!$this->_href) {
			$this->_href = '';
			if ($this->getData('href')) {
				$this->_href = $this->getData('href');
			} else if($this->getData('id_path')) {
				if($this->isPopup()) {
					$this->_href = '#';
				} else {
					$this->_href = Mage::helper('flippingbook')->getMagazineUrl($this->getMagazine());
				}
			}
		}

		return $this->_href;
	}

	/**
	 * Prepare flippingbook onclick.
	 *
	 * @return string
	 */
	public function getOnclick()
	{
		if(!$this->_onclick) {
			$this->_onclick = '';
			if ($this->getData('onclick')) {
				$this->_onclick = $this->getData('onclick');
			} else if($this->getData('id_path')) {
				if($this->isPopup()) {
					$url = Mage::helper('flippingbook')->getMagazineUrl($this->getMagazine());
					$width = $this->getPopupWidth();
					$height = $this->getPopupHeight();
					$this->_onclick = "popWin('{$url}', 'flippingbook', 'width={$width},height={$height}'); return false;";
				} else {
					$this->_onclick = '';
				}
			}
		}

		return $this->_onclick;
	}

	/**
	 * Prepare anchor title attribute using passed title
	 * as parameter or retrieve category title from DB using passed identifier or category id.
	 *
	 * @return string
	 */
	public function getTitle()
	{
		if (!$this->_title) {
			$this->_title = '';
			if ($this->getData('title') !== null) {
				// compare to null used here bc user can specify blank title
				$this->_title = $this->getData('title');
			} else if ($this->getData('id_path')) {
				$this->_title = $this->getMagazine()->getMagazineTitle();
			}
		}

		return $this->_title;
	}

	/**
	 * Prepare anchor text using passed text as parameter.
	 * If anchor text was not specified use title instead and
	 * if title will be blank string, page identifier will be used.
	 *
	 * @return string
	 */
	public function getAnchorText()
	{
		if ($this->getData('anchor_text')) {
			$this->_anchorText = $this->getData('anchor_text');
		} else if ($this->getData('id_path')) {
			$this->_anchorText = $this->getMagazine()->getMagazineTitle();
		} else if ($this->getTitle()) {
			$this->_anchorText = $this->getTitle();
		} else {
			$this->_anchorText = $this->getData('href');
		}

		return $this->_anchorText;
	}

	public function getThumbUrl()
	{
		if ($this->getData('thumbnail_url')) {
			$this->_thumbUrl = $this->getData('thumbnail_url');
		} else if ($this->getData('id_path')) {
			$this->_thumbUrl = $this->getMagazine()->getThumbUrl();
		}

		return $this->_thumbUrl;
	}

	public function isPopup()
	{
		return $this->getMagazine()->getMagazineViewMode();
	}

	public function getPopupWidth()
	{
		$magazine = $this->getMagazine();

		return $magazine->getResolutionWidth()*($magazine->getMagazineViewMode() + 1) + 150 >= 700 ? $magazine->getResolutionWidth()*($magazine->getMagazineViewMode() + 1) + 120 : 700;
	}

	public function getPopupHeight()
	{
		$magazine = $this->getMagazine();

		return $magazine->getResolutionHeight() + 200;
	}

	public function getMagazine()
	{
		$magazine = $this->getData('magazine');
		if (!$magazine) {
			$magazine = Mage::getModel('flippingbook/magazine')->load(intval($this->getData('id_path')));
			$this->setData('magazine', $magazine);
		}

		return $magazine;
	}
}

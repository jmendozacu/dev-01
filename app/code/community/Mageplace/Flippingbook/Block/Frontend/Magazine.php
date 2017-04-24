<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Block_Frontend_Magazine extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
	{
		parent::_prepareLayout();

		$magazine = $this->getMagazine();

		if ($magazine !== false && $head = $this->getLayout()->getBlock('head')) {
			$head->setTitle($this->htmlEscape($magazine->getName()) . ' - ' . $head->getTitle());
		}

		return $this;
	}


	public function getMagazine()
	{
		$magazine = $this->getData('magazine');
		if (is_null($magazine)) {
			$magazine = Mage::registry('flippingbook_current_magazine');
			$this->setData('magazine', $magazine);
		}

		return $magazine;
	}

	public function getHeight()
	{
		$height = $this->getData('height');
		if (is_null($height)) {
			$size = $this->_getSize();
			$height = $this->getData('height');
		}

		return $height;
	}

	public function getWidth()
	{
		$width = $this->getData('width');
		if (is_null($width)) {
			$size = $this->_getSize();
			$width = $this->getData('width');
		}

		return $width;
	}

	protected function _getSize()
	{
		$magazine = $this->getMagazine();
		if ($magazine->getIsPopupView()){
			$height = ($magazine->getResolutionHeight() + 130) . 'px';
			$width = (($magazine->getResolutionWidth()*($magazine->getMagazineViewStyle() + 1) + 120 >= 760) ? $magazine->getResolutionWidth()*($magazine->getMagazineViewStyle() + 1) + 120 : 760) . 'px';
		} else {
			if($magazine->getMagazinePopup()) {
				$width = '100%';
				$height = '100%';
			} else {
				$height = ($magazine->getResolutionHeight() + 130).'px';
				$width = (($magazine->getResolutionWidth()*($magazine->getMagazineViewStyle() + 1) + 120 >= 760) ? $magazine->getResolutionWidth()*($magazine->getMagazineViewStyle() + 1) + 120 : 760).'px';
			}
		}

		$this->setData('width', $width);
		$this->setData('height', $height);
	}


	public function getBaseUrl()
	{
		return urlencode(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
	}

    public function getPages()
    {
        $magazine = $this->getMagazine();
        $pages = Mage::getResourceModel('flippingbook/page_collection');
        $pages->addMagazineFilter($magazine)
            ->addIsActiveFilter()
            ->setOrderByPosition()
            ->getItems();

        return $pages;
    }

    public function getMagazineUrl($magazine)
    {
        return Mage::helper('flippingbook')->getMagazineUrl($magazine);
    }

    public function getPdfUrl($_magazine)
    {
        return Mage::helper('flippingbook')->getPathUrl('pdf').'/'.$_magazine->getMagazineBackgroundPdf();
    }

    public function getLineSpasing($_magazine)
    {
        $spasing = $_magazine->getLineSpacing();
        $value = Mage::getModel('flippingbook/adminhtml_system_config_source_template_lineSpacing')->getLabel($spasing);
        if($value) return $value;

        return  '17px';
    }

    public function getParagraphSpacing($_magazine)
    {
        $paragraph = $_magazine->getParagraphSpacing();
        $value = Mage::getModel('flippingbook/adminhtml_system_config_source_template_paragraphSpasing')->getLabel($paragraph);
        if($value) return $value;

        return  '17px';
    }

    public function getFontFamily($_magazine)
    {
        $font_family = $_magazine->getFontFamily();
        $value = Mage::getModel('flippingbook/adminhtml_system_config_source_template_fontfamily')->getLabel($font_family);
        if($value) return $value;

        return  '17px';
    }

    public function getFontSize($_magazine)
    {
        $font_family = $_magazine->getFontSize();
        $value = Mage::getModel('flippingbook/adminhtml_system_config_source_template_fontsize')->getLabel($font_family);
        if($value) return $value;

        return  '14px';
    }
}
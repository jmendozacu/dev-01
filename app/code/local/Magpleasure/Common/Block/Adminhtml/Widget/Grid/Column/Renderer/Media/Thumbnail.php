<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Magpleasure_Common
 */

class Magpleasure_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Media_Thumbnail
    extends Magpleasure_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Abstract
{
    public function getImageUrl($value)
    {
        $imageUrl = Mage::getBaseUrl('media').trim($value, "/");
        return $imageUrl;
    }

    public function getThumbnailUrl($value)
    {
        $imageUrl = $this->_getImageHelper()->init($value)->resize($this->getWidth(), $this->getHeight())->__toString();
        return $imageUrl;
    }

    /**
     * Retrieves Media Image Helper
     *
     * @return Magpleasure_Common_Helper_Image
     */
    protected function _getImageHelper()
    {
        return $this->_commonHelper()->getImage();
    }

    public function getWidth()
    {
        return
            $this->getColumn()->hasData('thumbnail_width') ?
            $this->getColumn()->getData('thumbnail_width') :
            Mage::getStoreConfig('magpleasure/thumbnail/width')
            ;
    }

    public function getHeight()
    {
        return
            $this->getColumn()->hasData('thumbnail_height') ?
            $this->getColumn()->getData('thumbnail_height') :
            Mage::getStoreConfig('magpleasure/thumbnail/height')
            ;
    }

    public function render(Varien_Object $row)
    {
        $value = $this->_getValue($row);

        if ($value){
            $height = $this->getHeight();
            $width = $this->getWidth();
            try {
                $imageUrl = $this->getImageUrl($value);
                $thumbnailUrl = $this->getThumbnailUrl($value);

                $labelKey =
                    $this->getColumn()->getData('label_field') ?
                    $this->getColumn()->getData('label_field') :
                    'label'
                ;

                $label = $row->getData($labelKey);
                $id = $row->getId();

                return "
                <div class=\"mp-common-image\" style=\"width: {$width}px; height: {$height}px;\">
                    <a class=\"mp-common-image-link\" href=\"{$imageUrl}\" target=\"_blank\" onclick=\"disabledEventPropagation();\" >
                        <img width=\"{$width}px\" height=\"{$height}px\" src=\"{$thumbnailUrl}\" alt=\"{$label}\" />
                    </a>
                </div>
                 ";
            } catch (Exception $e) {
                return "";
            }
        }
        return "";
    }



}
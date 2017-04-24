<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Mageplace_Flippingbook
 */
class Mageplace_Flippingbook_Helper_Community extends Mage_Core_Helper_Abstract
{
    const FLIPPINGBOOK_URL_NAME = 'flippingbook';

    protected $_config = array();
    protected $_isDir = 0;

    protected $_font_family = array(
        0  => '"Times New Roman", Times, serif',
        1  => 'Georgia, serif',
        2  => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
        3  => 'Arial, Helvetica, sans-serif',
        4  => '"Arial Black", Gadget, sans-serif',
        5  => '"Comic Sans MS", cursive, sans-serif',
        6  => 'Impact, Charcoal, sans-serif',
        7  => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
        8  => 'Tahoma, Geneva, sans-serif',
        9  => '"Trebuchet MS", Helvetica, sans-serif',
        10 => 'Verdana, Geneva, sans-serif',
        11 => '"Courier New", Courier, monospace',
        12 => '"Lucida Console", Monaco, monospace'
    );

    protected $_font_size = array(
        1 => "10px",
        2 => "12px",
        3 => "14px",
        4 => "16px",
        5 => "18px",
        6 => "20px",
        7 => "22px"
    );

    protected $_paragraph_spacing = array(
        1  => "0",
        2  => "3px",
        3  => "5px",
        4  => "7px",
        5  => "9px",
        6  => "12px",
        7  => "14px",
        8  => "16px",
        9  => "18px",
        10 => "20px",
    );

    protected $_line_spacing = array(
        1 => "15px",
        2 => "17px",
        3 => "19px",
        4 => "21px",
        5 => "23px",
        6 => "25px",
        7 => "27px",
        8 => "29px",
        9 => "31px"
    );


    public function getConfig($key, $section)
    {
        if (is_null($section) || is_null($key)) {
            return null;
        }

        $section = strtolower($section);
        $key     = strtolower($key);
        if (empty($this->_config[$this->_isDir][$section][$key])) {
            $value                         = $this->_getConfigValue($key, $section);
            $this->_config[$section][$key] = $value;
        }

        return @$this->_config[$section][$key];
    }

    protected function _getConfigValue($key, $section)
    {
        $value = Mage::getStoreConfig('flippingbook/' . $section . '/' . $key);

        if (preg_match_all('/\{\{([^\}]*)\}\}/i', $value, $dirs) && !empty($dirs[1])) {
            if ($this->_isDir) {
                $mage_dirs = Mage::getConfig()->getOptions()->getData();
            }

            foreach ($dirs[1] as $key => $dir) {
                if ($this->_isDir) {
                    if (array_key_exists($dir, $mage_dirs)) {
                        $value = str_replace($dirs[0][$key], $mage_dirs[$dir], $value);
                    } else {
                        $node_name = str_replace('_dir', '', $dir);
                        $value     = str_replace($dirs[0][$key], $this->_getConfigValue($node_name, $section), $value);
                    }

                } else {
                    $node_name = str_replace('_dir', '', $dir);

                    try {
                        $path  = Mage::getBaseUrl($node_name);
                        $path  = preg_replace('/\/$/', '', $path);
                        $value = str_replace($dirs[0][$key], $path, $value);
                    } catch (Exception $e) {
                        $value = str_replace($dirs[0][$key], $this->_getConfigValue($node_name, $section), $value);
                    }
                }
            }
        }

        return $value;
    }

    public function getDir($key)
    {
        $this->_isDir = 1;

        return $this->getConfig($key, 'filesystem');
    }

    public function getPathUrl($key)
    {
        $this->_isDir = 0;

        return $this->getConfig($key, 'filesystem');
    }

    public function getLastPostition($magazine)
    {
        $magazine_id  = $magazine->getMagazineId();
        $page_sort_id = Mage::getModel('flippingbook/page')
            ->getCollection()
            ->addFieldToFilter('page_magazine_id', $magazine_id)
            ->setOrder('page_sort_order')
            ->load()
            ->getFirstItem()
            ->getPageSortOrder();
        if ($page_sort_id) {
            $page_sort_id++;
            return $page_sort_id;
        }

        return 0;
    }

    public function getFlashMagazineUrl()
    {
        return Mage::getUrl(self::FLIPPINGBOOK_URL_NAME);
    }

    public function getMagazineUrl($magazine, $action = null, $params = array())
    {
        if (is_int($magazine)) {
            $magazine = Mage::getModel('flippingbook/magazine')->load($magazine);
        }
        if (is_null($action)) {
            $action = $magazine->getMagazineViewMode() ? 'popup' : 'view';
        }

        $params['id'] = $magazine->getId();

        return Mage::getUrl(self::FLIPPINGBOOK_URL_NAME . '/magazine/' . $action, $params);
    }


    public function canShowMagazine($magazine)
    {
        if (is_int($magazine)) {
            $magazine = Mage::getModel('flippingbook/magazine')->load($magazine);
        }

        if (!($magazine instanceof Mageplace_Flippingbook_Model_Magazine)) {
            return false;
        }

        if (!$magazine->getId()) {
            return false;
        }

        if (!$magazine->getIsActive()) {
            return false;
        }

        return true;
    }

    public function getFontsFamily()
    {
        return $this->_font_family;
    }

    public function getFontsSize()
    {
        return $this->_font_size;
    }

    public function getParagraphSpacing()
    {
        return $this->_paragraph_spacing;
    }

    public function getLineSpacing()
    {
        return $this->_line_spacing;
    }

    public function getRootTemplate()
    {
        $magazine = Mage::registry('flippingbook_current_magazine');
        $template = $magazine->getRootTemplate();

        if($template && !$magazine->getIsPopupView()){
            $temp_layout = Mage::getModel('page/config')->getPageLayout($template);
            if($temp_layout){
                return $temp_layout->getTemplate();
            }
        }

        return false;
    }
}

<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Model_Adminhtml_System_Config_Source_Store
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @return array
     */
    public function toOptionArray($withEmpty = true)
    {
        if (!$this->_options) {
            $this->_options = array();
            if ($withEmpty) {
                $this->_options[] = array(
                    'value' => '',
                    'label' => Mage::helper('adminhtml')->__('-- Please Select --'),
                );
            }

            $this->_options[] = array(
                'value' => '0',
                'label' => Mage::helper('adminhtml')->__('All Store Views'),
            );

            $websites = Mage::app()->getWebsites();
            foreach ($websites as $website) {
                /** @var Mage_Core_Model_Website $website */
                $stores = array();
                foreach ($website->getStores() as $store) {
                    /** @var Mage_Core_Model_Store $store */
                    $stores[] = array(
                        'value' => $store->getId(),
                        'label' => $store->getName(),
                    );
                }
                $this->_options[] = array(
                    'value' => $stores,
                    'label' => $website->getName(),
                );
            }
        }

        return $this->_options;
    }
}
<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Block_Adminhtml_Customer_Group_Switcher extends Mage_Adminhtml_Block_Template
{
    /**
     * @return int
     * @throws Exception
     */
    public function getCustomerGroupId()
    {
        return $this->getRequest()->getParam('group');
    }

    /**
     * @return Mage_Customer_Model_Resource_Group_Collection
     */
    public function getCustomerGroups()
    {
        return Mage::getModel('customer/group')->getCollection();
    }

    /**
     * @return string
     */
    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }

        return $this->getUrl('*/*/*', array('_current' => true, 'group' => null));
    }
}
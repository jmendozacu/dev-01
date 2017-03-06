<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Block_Adminhtml_Cms_Page_Edit extends Mage_Adminhtml_Block_Cms_Page_Edit
{
    /**
     * Initialization
     */
    public function __construct()
    {
        parent::__construct();

        $this->_removeButton('back');
        $this->_removeButton('reset');
        $this->_removeButton('saveandcontinue');

        $this->_updateButton('save', 'onclick', "editForm.submit('" . $this->getFormActionUrl() . "')");

        /** @var Bubble_CmsTree_Model_Cms_Page $page */
        $page = Mage::registry('cms_page');

        if ($page && $page->getId() && $page->getParentId()) {
            $this->_addButton('delete', array(
                'label'   => Mage::helper('cms')->__('Delete Page'),
                'onclick' => sprintf("deleteConfirm('%s', '%s')",
                    Mage::helper('adminhtml')->__('Are you sure you want to do this?'),
                    Mage::helper('adminhtml')->getUrl('*/*/delete', array('page_id' => $page->getId()))
                ),
                'class'   => 'scalable delete',
                'level'   => -1
            ));
        }

        if ($page && $page->getId()) {
            $this->_addButton('duplicate', array(
                'label'      => $this->__('Duplicate To...'),
                'onclick'    => "showPopupDuplicate();",
                'class'      => 'add',
                'level'      => 0,
            ));
            $this->_addButton('preview', array(
                'label'      => $this->__('Preview Page'),
                'class'      => 'scalable go',
                'onclick'    => "previewAction('edit_form', editForm, '" . $this->getUrl('*/*/previewPage') . "')",
                'level'      => 0,
            ));
        }
    }

    /**
     * @return string
     */
    public function getDuplicateFormAction()
    {
        /** @var Bubble_CmsTree_Model_Cms_Page $page */
        $page = Mage::registry('cms_page');

        $url = $this->getUrl('*/*/duplicate', array(
            'page_id' => $page->getId(),
        ));

        return $url;
    }

    /**
     * @return string
     */
    public function getDuplicateStoreSelectHtml()
    {
        /** @var Bubble_CmsTree_Model_Cms_Page $page */
        $page = Mage::registry('cms_page');

        $options = Mage::getModel('bubble_cmstree/adminhtml_system_config_source_store')->toOptionArray(false);

        return $this->getLayout()->createBlock('core/html_select')
            ->setId('duplicate-store-select')
            ->setName('store')
            ->setClass('validate-select')
            ->setOptions($options)
            ->setExtraParams('style="width:auto;"')
            ->setValue($page->getStoreId())
            ->toHtml();
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        $page = Mage::registry('cms_page');
        if ($page && $page->getId()) {
            return Mage::helper('cms')->__("Edit Page '%s'", $this->htmlEscape($page->getTitle()))
                . ' (ID: ' . $page->getId() . ')';
        }

        return Mage::helper('cms')->__('New Page');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getFormActionUrl()
    {
        $args = array();

        if ($this->getRequest()->has('store')) {
            $args['store'] = $this->getRequest()->getParam('store');
        }

        if ($this->getRequest()->has('parent')) {
            $args['parent'] = $this->getRequest()->getParam('parent');
        }

        return $this->getUrl('*/*/save', $args);
    }

    /**
     * @return string
     */
    public function getTabsHtml()
    {
        return $this->getLayout()->getBlock('cms_page_edit_tabs')->removeTab('hierarchy')->toHtml();
    }
}
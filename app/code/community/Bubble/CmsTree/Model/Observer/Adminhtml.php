<?php
/**
 * @category    Bubble
 * @package     Bubble_CmsTree
 * @version     2.1.0
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_CmsTree_Model_Observer_Adminhtml
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function prepareForm(Varien_Event_Observer $observer)
    {
        /** @var Bubble_CmsTree_Model_Cms_Page $page */
        $page = Mage::registry('cms_page');
        /** @var Varien_Data_Form $form */
        $form = $observer->getEvent()->getForm();
        $includeInMenuDisabled = $urlKeyDisabled = false;

        // add our 'url_key' field so that users can set a URL identifier independent of the CMS page title
        if ($page->getPageId() && $page->isRoot()) {
            // disable the 'url key' and 'include in menu' configuration options for the root CMS page
            $includeInMenuDisabled = $urlKeyDisabled = true;
        }

        $form->getElement('base_fieldset')->addField('url_key', 'text', array(
            'name'     => 'url_key',
            'label'    => Mage::helper('cms')->__('URL Key'),
            'title'    => Mage::helper('cms')->__('URL Key'),
            'note'     => Mage::helper('cms')->__(
                'Leave blank for automatic generation.<br />URL is relative to parent URL. Current URL: <a href="%s" target="_blank">%s</a>',
                $page->getUrl($page->getStoreId()),
                $page->getUrl($page->getStoreId())
            ),
            'value'    => $page->getIdentifier(),
            'disabled' => $urlKeyDisabled
        ));

        if (!Mage::app()->isSingleStoreMode() && $page->getStoreId() == 0) {
            $form->getElement('base_fieldset')
                ->removeField('stores');
            $form->getElement('base_fieldset')->addField('stores', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => false,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
            ));
        }

        $form->getElement('base_fieldset')->addField('include_in_menu', 'select', array(
            'name'     => 'include_in_menu',
            'label'    => Mage::helper('bubble_cmstree')->__('Include in Navigation Menu'),
            'title'    => Mage::helper('bubble_cmstree')->__('Include in Navigation Menu'),
            'note'     => Mage::helper('bubble_cmstree')->__('Will be ignored if global config is set to "No", which is default value.'),
            'values'   => array(
                '1' => Mage::helper('adminhtml')->__('Yes'),
                '0' => Mage::helper('adminhtml')->__('No')
            ),
            'disabled' => $includeInMenuDisabled,
        ));

        $form->getElement('base_fieldset')->addField('manage_versions', 'select', array(
            'name'     => 'manage_versions',
            'label'    => Mage::helper('bubble_cmstree')->__('Manage Versions'),
            'title'    => Mage::helper('bubble_cmstree')->__('Manage Versions'),
            'note'     => Mage::helper('bubble_cmstree')->__('Indicates whether you want to handle versioning on this page or not.'),
            'values'   => array(
                '1' => Mage::helper('adminhtml')->__('Yes'),
                '0' => Mage::helper('adminhtml')->__('No')
            ),
        ));

        $form->getElement('base_fieldset')
            ->removeField('identifier')
            ->removeField('store_id');
        $form->addField('store_id', 'hidden', array('name' => 'store'));
        $form->addField('parent_id', 'hidden', array('name' => 'parent'));
        $form->addField('identifier', 'hidden', array('name' => 'identifier'));

        $request = Mage::app()->getRequest();
        $form->addField('current_tab', 'hidden', array('name' => 'tab', 'value' => $request->getParam('tab')));

        if (null === $page->getIncludeInMenu() && Mage::getStoreConfigFlag('bubble_cmstree/general/include_in_menu')) {
            $page->setIncludeInMenu(true);
        }
    }

    /**
     * @return void
     */
    public function renderAdminEditPageBefore()
    {
        if ($data = Mage::getSingleton('admin/session')->getRestoreVersionData()) {
            Mage::getSingleton('admin/session')->unsRestoreVersionData();
            $page = Mage::registry('cms_page');
            if ($page && $page->getId() == $data['page_id']) {
                $page->addData($data);
            }
        }
    }
}
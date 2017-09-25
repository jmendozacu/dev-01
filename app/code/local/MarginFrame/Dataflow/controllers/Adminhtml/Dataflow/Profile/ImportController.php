<?php

class MarginFrame_Dataflow_Adminhtml_Dataflow_Profile_ImportController
    extends Mage_Adminhtml_Controller_Action
{
    /** @var MarginFrame_Dataflow_Helper_Data */
    protected $_helper;

    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Initialize helper
     */
    protected function _construct()
    {
        $this->_helper = Mage::helper('marginframe_dataflow');
        parent::_construct();
    }
    
    /**
     * Init navigation and breadcrumbs
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/convert')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Dataflow'), Mage::helper('adminhtml')->__('Task'));
        
        return $this;
    }

    /**
     * Profiles grid
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('marginframe_dataflow/adminhtml_profile_import'));
        $this->renderLayout();
    }

    /**
     * Create new profile
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Create/Edit profile
     */
    public function editAction()
    {
        /** @var $profile MarginFrame_Dataflow_Model_Profile_Import */
        $profile = Mage::getModel('marginframe_dataflow/profile_import');
        if ($id = $this->getRequest()->getParam('id')) {
            $profile->load($id);
        }
        Mage::register('profile', $profile);
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('marginframe_dataflow/adminhtml_profile_import_edit'));
        $this->_addLeft($this->getLayout()->createBlock('marginframe_dataflow/adminhtml_profile_import_edit_tabs'));
        $this->renderLayout();
    }

    /**
     * @param array $data
     * @return MarginFrame_Dataflow_Model_Profile_Import
     */
    protected function _saveProfile(array $data)
    {
        /** @var $profile MarginFrame_Dataflow_Model_Profile_Import */
        $profile = Mage::getModel('marginframe_dataflow/profile_import');
        if ($this->getRequest()->getParam('id')) {
            $profile->load($this->getRequest()->getParam('id'));
        }
        $mapping = array();
        if (isset($data['mapping']) && is_array($data['mapping'])) {
            foreach ($data['mapping'] as $mappingData) {
                $mapping[$mappingData['number']] = $mappingData['code'];
            }
            unset($data['mapping']);
        }
        $profile->addData($data);
        $profile->setData('mapping', $mapping);
        $profile->setData('parser_model', $this->_helper->getDefaultParserModel());

        $profile->save();

        return $profile;
    }

    /**
     * Save profile data
     */
    public function saveAction()
    {
        try {
            if ($this->getRequest()->isPost()) {
                $this->_saveProfile($this->getRequest()->getPost());
                $this->_getSession()->addSuccess($this->__('Profile saved successfully.'));
                $this->_redirect('*/*');
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectReferer();
        }
    }

    /**
     * Delete profile
     */
    public function deleteAction()
    {
        try {
            /** @var $profile MarginFrame_Dataflow_Model_Profile_Import */
            $profile = Mage::getModel('marginframe_dataflow/profile_import');
            $profile->load($this->getRequest()->getParam('id'));
            if (!$profile->getId()) {
                Mage::throwException($this->__('Import no longer exists.'));
            } $profile->delete();
            $this->_getSession()->addSuccess($this->__('Profile deleted successfully.'));
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectReferer();
        }
        $this->_redirect('*/*');
    }

    /**
     * Save profile and run
     */
    public function saveAndRunAction()
    {
        try {
            if ($this->getRequest()->isPost()) {
                $profile = $this->_saveProfile($this->getRequest()->getPost());
                $this->_getSession()->addSuccess($this->__('Profile saved successfully.'));
                $this->_redirect('*/*/run', array('id' => $profile->getId()));
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectReferer();
        }
    }
    
    public function runAction()
    {
        /** @var $profile MarginFrame_Dataflow_Model_Profile_Import */
        $profile = Mage::getModel('marginframe_dataflow/profile_import');
        $profile->load($this->getRequest()->getParam('id'));

        Mage::register('profile', $profile);
        if (!empty($_FILES)) {
            try {
                if (!$profile->getId()) {
                   throw new MarginFrame_Dataflow_Exception($this->__('Profile no longer exists.'));
                }
                $fileData = array_shift($_FILES);
                $filePath = $fileData['tmp_name'];
                $profile->run($filePath);
                $this->_redirect('*/*');
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirectReferer();
            }
        } else {
            $this->_forward('file');
        }
    }
    
    public function fileAction()
    {
        try {
            /** @var $profile MarginFrame_Dataflow_Model_Profile_Import */
            $profile = Mage::getModel('marginframe_dataflow/profile_import');
            $profile->load($this->getRequest()->getParam('id'));
            if (!$profile->getId()) {
                throw new MarginFrame_Dataflow_Exception($this->__('Profile no longer exists.'));
            }

            $this->_initAction();
            $this->_addContent($this->getLayout()->createBlock('marginframe_dataflow/adminhtml_profile_import_file'));
            $this->renderLayout();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*');
        }
    }

    /**
     * Profiles grid
     */
    public function scheduleAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('marginframe_dataflow/adminhtml_profile_schedule'));
        $this->renderLayout();
    }
}

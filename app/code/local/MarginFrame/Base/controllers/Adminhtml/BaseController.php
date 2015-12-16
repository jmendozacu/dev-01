<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Base
 */

class MarginFrame_Base_Adminhtml_BaseController extends Mage_Adminhtml_Controller_Action
{
    protected $_moduleHelper;
    
    protected function _getModuleHelper($code)
    {
        if (!$this->_moduleHelper)
        {
            $this->_moduleHelper = Mage::helper("ambase/module")->init($code);
        }
        
        return $this->_moduleHelper;
    }
    
    public function closeUpdateAction()
    {
        $code = Mage::app()->getRequest()->getParam('code');
        
        $moduleHelper = $this->_getModuleHelper($code);
        
        if ($moduleHelper->isNewVersionAvailable())
        {
            $moduleHelper->setModuleUpdated();
        }
    }
    
    public function closePromoAction()
    {
        $collection = Mage::helper("ambase/promo")->getNotificationsCollection();
        
        foreach($collection as $notification)
        {
            $notification->setIsRead(true);
            $notification->save();
        }
    }
    
    public function ajaxAction()
    {
        $helper = Mage::helper("ambase");
        print $helper->ajaxHtml();
    }
    
    public function fixAction()
    {
        $object = Mage::app()->getRequest()->getParam('object');
        $module = Mage::app()->getRequest()->getParam('module');
        $rewrite = Mage::app()->getRequest()->getParam('rewrite');
        if ($module && $rewrite && $object){
            
            try {
                $conflict = Mage::getModel("ambase/conflict");
                $conflict->fix($object, $module, $rewrite);
                
                foreach($conflict->log() as $m)
                    Mage::getSingleton('adminhtml/session')->addNotice($m);
                        
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            
            
            $this->_redirect("adminhtml/system_config/edit", array(
                "section" => "ambase",
                "autoload" => 1
            ));
        }
    }
    
    public function rollbackAction()
    {
        $object = Mage::app()->getRequest()->getParam('object');
        $module = Mage::app()->getRequest()->getParam('module');
        $rewrite = Mage::app()->getRequest()->getParam('rewrite');
        if ($module && $rewrite && $object){
            try {
                $conflict = Mage::getModel("ambase/conflict");
                $conflict->rollback($object, $module, $rewrite);
                
                foreach($conflict->log() as $m)
                    Mage::getSingleton('adminhtml/session')->addNotice($m);
                
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
                        
            $this->_redirect("adminhtml/system_config/edit", array(
                "section" => "ambase",
                "autoload" => 1
            ));
        }
    }
}  
?>
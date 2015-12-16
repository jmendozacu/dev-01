<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Base
 */ 
class MarginFrame_Base_Block_Adminhtml_Debug_Rewrite extends MarginFrame_Base_Block_Adminhtml_Debug_Base
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('marginframe/ambase/debug/rewrite.phtml');        
    }
    
    function getRewritesList(){
        return Mage::helper("ambase")->getRewritesList();
    }
}
?>
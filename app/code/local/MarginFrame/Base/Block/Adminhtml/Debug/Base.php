<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Base
 */ 
class MarginFrame_Base_Block_Adminhtml_Debug_Base extends Mage_Adminhtml_Block_Widget_Form
{
    function getClassPath($rewrites, $codePool, $rewriteIndex){
        return MarginFrame_Base_Model_Conflict::getClassPath($codePool[$rewriteIndex], $rewrites[$rewriteIndex]);
    }
}
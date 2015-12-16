<?php
/**
 * @author MarginFrame Team
 * @copyright Copyright (c) 2015 MarginFrame (http://www.marginframe.com)
 * @package MarginFrame_Base
 */  
class MarginFrame_Base_Helper_Promo extends Mage_Core_Helper_Abstract
{
    function getNotificationsCollection()
    {
        $collection = Mage::getModel("adminnotification/inbox")->getCollection();
        
        $collection->getSelect()
            ->where('title like "%marginframe%" or description like "%marginframe%" or url like "%marginframe%"')
            ->where('is_read != 1')
            ->where('is_remove != 1');
            
        return $collection;
    }
    
    function isSubscribed()
    {
        return Mage::getStoreConfig('ambase/feed/promo') == 1;
    }
}

?>
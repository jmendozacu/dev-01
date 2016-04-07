<?php
class Magebuzz_RegisterSource_Model_Observer
{

    public function checkSource(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $customer = $event->getCustomer();
        if($customer->getId()){
            $customer->setSource('Website');
        }
        $facebook_user_id = $customer['amajaxlogin_fb_id'];
        if ($facebook_user_id) {
            $customer->setSource('Facebook');
        }
    }

}

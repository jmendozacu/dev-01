<?php
class Magebuzz_RegisterSource_Model_Observer
{

    public function checkSource(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $customer = $event->getCustomer();
        if ($customer->getId()) {
            $customer->setCustomerSource('Website')->save();
        }
    }

}

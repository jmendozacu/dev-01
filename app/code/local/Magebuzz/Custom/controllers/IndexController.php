<?php
class Magebuzz_Custom_IndexController extends Mage_Core_Controller_Front_Action
{
    public function checkItemAction()
    {
        $wlitemadded = array();
        $wlcoll = Mage::helper('wishlist')->getWishlistItemCollection();
        foreach ($wlcoll as $_item) {
            $wlitemadded[] = $_item->getProduct()->getId();
        }

        $result = array(
            'wlitemadded' => $wlitemadded,
            'form_key' => Mage::getSingleton('core/session')->getFormKey()
        );

        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($result));
    }
}
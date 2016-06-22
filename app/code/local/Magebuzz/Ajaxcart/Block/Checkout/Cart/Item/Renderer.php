<?php
class Magebuzz_Ajaxcart_Block_Checkout_Cart_Item_Renderer extends  Mage_Checkout_Block_Cart_Item_Renderer{

    public function getDeleteUrl()
    {
        if ($this->hasDeleteUrl()) {
            return $this->getData('delete_url');
        }

        return $this->getUrl(
            'checkout/cart/delete',
            array(
                'id'=>$this->getItem()->getId()
            )
        );
    }
}

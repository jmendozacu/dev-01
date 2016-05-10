<?php
class MarginFrame_Paysbuy_Block_Info_Paysbuy extends Mage_Payment_Block_Info
{
    /**
     * Init default template for block
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Paysbuy/info/Paysbuy.phtml');
    }

   	public function getPlanText(){
        
        return Mage::getStoreConfig('payment/Paysbuy/infoimage');
    }
}
?>
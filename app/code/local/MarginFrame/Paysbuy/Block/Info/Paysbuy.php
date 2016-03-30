<?php
class MarginFrame_Paysbuy_Block_Info_Paysbuy extends Mage_Payment_Block_Info
{
    /**
     * Init default template for block
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Paysbuy/info/paysbuy.phtml');
    }

    /**
     * Retrieve credit card type name
     *
     * @return string
     */
    public function getPaysbuyTypeName()
    {
        $types = Mage::getSingleton('Paysbuy/config')->getPaysbuyTypes();
        if (isset($types[$this->getInfo()->getPaysbuyType()])) {
            return $types[$this->getInfo()->getPaysbuyType()];
        }
        return $this->getInfo()->getPaysbuyType();
    }

   
}
?>
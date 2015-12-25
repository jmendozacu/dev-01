<?php
/**
 * @copyright Amasty 2010
 */

class Amasty_ShippingPerItem_Model_Carrier_Shippingperitem extends Mage_Shipping_Model_Carrier_Abstract
{
    protected $_code = 'amperitem';

    /**
     * Collect rates for this shipping method based on information in $request
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        // skip if not enabled
        if (!$this->getConfigData('active')) {
            return false;
        }

        // this object will be returned as result of this method
        // containing all the shipping rates of this method
        $result = Mage::getModel('shipping/rate_result');

        // create new instance of method rate
        $method = Mage::getModel('shipping/rate_result_method');

        // record carrier information
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        // record method information
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        
        // load all products and check if they have a specific shipping data
        $ids = $this->_getApplicapleItemIds($request); // hash of id=>qty
        $attr = 'am_shipping_peritem';
        $collection = Mage::getModel('catalog/product')->getCollection() 
                 ->addIdFilter(array_keys($ids))
                 ->joinAttribute($attr, 'catalog_product/'.$attr, 'entity_id', null, 'left')
                ;
         // calculate initial price        
         $price = floatVal($this->getConfigData('base_rate'));
         $hasIndividualRate = false;
         
         $rates = array();
         foreach ($collection as $product){
            $rate = 0; 
            if ($product->getData($attr) > 0.00001){
                $rate  = $product->getData($attr);  
                $hasIndividualRate = true;
            } 
            elseif ($this->getConfigData('use_default_rate'))
                $rate  = $this->getConfigData('default_rate');
                
            $qty = 1;
            if ($this->getConfigData('calc_separately'))
                $qty = $ids[$product->getId()];
                
            $rates[] = (1.0 * $rate * $qty);   
         } 
         
         $price = 0;
         if ($this->getConfigData('use_max')){
             $price += max($rates);
         }
         else {
             $price += array_sum($rates);
         }
            
         if (!$hasIndividualRate && $this->getConfigData('individual_rate_only')) {
             return false;
         }      
        
        // bounding
        $minBound = $this->getConfigData('min');
        if ($minBound){
            $price = max($minBound, $price);
        }
        $maxBound = $this->getConfigData('max');
        if ($maxBound){
            $price = min($maxBound, $price);
        }
        
        if ($request->getFreeShipping() === true) {
            $price = 0.0;
        }        

        $method->setCost($price);
        $method->setPrice($price);

        // add this rate to the result
        $result->append($method);
        
        return $result;
    } 


    public function getAllowedMethods()
    {
        return array($this->_code => $this->getConfigData('name'));
    }
    
    
    private function _getApplicapleItemIds($request)
    {
        // loop throug all items in the cart and collect applicapable items
        $ids = array();
        foreach ($request->getAllItems() as $item) {
            if ($item->getParentItem() || $item->getProduct()->isVirtual()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isShipSeparately()){
                foreach ($item->getChildren() as $child) {
                    if ($child->getFreeShipping() || $child->getProduct()->isVirtual()){
                        continue;
                    }
                    $id = $child->getProduct()->getId();
                    if (isset($ids[$id])){
                        $ids[$id] += $child->getQty();    
                    }
                    else {
                        $ids[$id] = $child->getQty();
                    }
                }
            }
            elseif (!$item->getFreeShipping()) {
                    $id = $item->getProduct()->getId();
                    if (isset($ids[$id])){
                        $ids[$id] += $item->getQty();    
                    }
                    else {
                        $ids[$id] = $item->getQty();
                    }
            }
        }
        return $ids;
    }
}
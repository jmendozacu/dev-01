<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Birth
 */
class Amasty_Birth_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function generateCoupon($type, $store=null, $customerEmail='')
    {
      	$couponData = array();
        $couponData['name']      = 'Special Coupon: ' . ucfirst($type) . ' for ' . $customerEmail;
        $couponData['is_active'] = 1;
        // all websites here:
        $couponData['website_ids'] =  array_keys(Mage::app()->getWebsites(true));
        
        $couponData['coupon_type'] = 2;  // for mahento 1.4.1.1
        $couponData['coupon_code'] = strtoupper(uniqid()); 
        
        $maxUses = intVal(Mage::getStoreConfig('ambirth/'.$type.'/coupon_uses'));
        $usesPerCustomer = intVal(Mage::getStoreConfig('ambirth/'.$type.'/uses_per_customer'));
        $couponData['uses_per_coupon']   = $maxUses;
        $couponData['uses_per_customer'] = $usesPerCustomer;
        $couponData['from_date'] = ''; //current date

        $days = intVal(Mage::getStoreConfig('ambirth/'.$type.'/coupon_days', $store));
        $date = date('Y-m-d', time() + $days*24*3600);
        $couponData['from_date'] = date('Y-m-d');
        $couponData['to_date'] = $date;

        $couponData['simple_action']   = Mage::getStoreConfig('ambirth/'.$type.'/coupon_type', $store);
        $couponData['discount_amount'] = Mage::getStoreConfig('ambirth/'.$type.'/coupon_amount', $store);
        
        if ('ampromo_cart' == $couponData['simple_action']){
            $couponData['promo_sku']       = $couponData['discount_amount'];
            $couponData['discount_amount'] = 0; 
        }
        
        $couponData['conditions'] = array(
            '1' => array(
                'type'       => 'salesrule/rule_condition_combine',
                'aggregator' => 'all',
                'value'      => 1,
                'new_child'  =>'', 
            ),
            '1--1' => array(
                'type'      => 'salesrule/rule_condition_address',
                'attribute' => 'base_subtotal',
                'operator'  => '>=',
                'value'     => floatVal(Mage::getStoreConfig('ambirth/'.$type.'/min_order', $store)),
            ),             
        );
        
        $couponData['actions'] = array(
            1 => array(
                'type'       => 'salesrule/rule_condition_product_combine',
                'aggregator' => 'all',
                'value'      => 1,
                'new_child'  =>'', 
            )
        );

        //compatibility with aitoc's individ promo module
        $couponData['customer_individ_ids'] = array();
        
        //create for all customer groups
        $couponData['customer_group_ids'] = array();
        if(!Mage::getStoreConfig('ambirth/'.$type.'/customer_group', $store)){
            $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->load();

            $found = false;
            foreach ($customerGroups as $group) {
                if (0 == $group->getId()) {
                    $found = true;
                }
                $couponData['customer_group_ids'][] = $group->getId();
            }
            if (!$found) {
                $couponData['customer_group_ids'][] = 0;
            }    
        }
        else{
            $groups = Mage::getStoreConfig('ambirth/'.$type.'/customer_group', $store);
            $couponData['customer_group_ids'] = explode ( ',', $groups );
        }
        
        try { 
            Mage::getModel('salesrule/rule')
                ->loadPost($couponData)
                ->save();      
        } 
        catch (Exception $e){
            //print_r($e); exit;
            $couponData['coupon_code'] = '';   
        }
        
        return $couponData['coupon_code'];

    }   
}
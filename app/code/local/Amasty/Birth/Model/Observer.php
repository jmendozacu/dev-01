<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Birth
 */
class Amasty_Birth_Model_Observer
{
    public function newsletterSubscriberSaveBefore(Varien_Event_Observer $observer)
    {
        $subscriber = $observer->getEvent()->getSubscriber();
        $email = $subscriber->getEmail();

        $data = $subscriber->getResource()->loadByEmail($email);
        if(Mage::getStoreConfig('ambirth/newsletter/enabled')){
            $couponName = 'Special Coupon: Newsletter for ' . $email;
            $firstCoupon = true;
            $coupon = "";

            $collection = Mage::getModel('salesrule/rule')->getResourceCollection();
            foreach ($collection as $rule) {
                if ($rule->getName() == $couponName) {
                    $firstCoupon = false;
                    $coupon = $rule->getCouponCode();
                }
            }
            if($firstCoupon) {
                $subscriber->_coupon = Mage::helper('ambirth')->generateCoupon('newsletter', 0, $email);
                $subscriber->setCoupon($subscriber->_coupon);
            }
            else{
                $subscriber->_coupon = $coupon;
                $subscriber->setCoupon($coupon);
            }
        }

        return $this;
    }

    private function _debug($str)
    {
        if(isset($_GET['debug'])) echo "\r\n<br><br>" . $str;
    }


    public function send()
    {
        $types = array('reg', 'birth', 'activity', 'wishlist', 'regbirth');
        foreach ($types as $type){
            if (Mage::getStoreConfig('ambirth/' . $type . '/enabled')){
                $this->_debug("Called " . '_send' . ucfirst($type) . 'Coupon');
                call_user_func(array($this, '_send' . ucfirst($type) . 'Coupon'));
            }
        }
        $this->_removeOldCoupons();

        return $this;
    }

    protected function _getCollection()
    {
        // !! todo add condition (not in log table)
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->setPageSize(300)
            ->setCurPage(1);

        return $collection;
    }

    protected function _sendBirthCoupon()
    {
        $days = intVal(Mage::getStoreConfig('ambirth/birth/days'));
        $time = time();
        if ($days > 0) // after birthday
            $time = strtotime("-$days days");
        else {
            $days = abs($days);
            $time = strtotime("+$days days");
        }

        $collection = $this->_getCollection()
            ->joinAttribute('dob','customer/dob', 'entity_id');
        $select =  $collection->getSelect();
        $select->where(new Zend_Db_Expr(
                "DATE_FORMAT(`at_dob`.`value`, '%m-%d') = '".date('m-d', $time)."'"
            )
        )
        ;

        foreach ($collection as $customer){
            $this->_emailToCustomer($customer, 'birth');
        }
    }

    protected function _sendRegbirthCoupon()
    {
        $time = time();

        $collection = $this->_getCollection();
        $select =  $collection->getSelect();
        $select->where(new Zend_Db_Expr(
                    "DATE_FORMAT(`created_at`, '%m-%d') = '".date('m-d', $time)."'"
                ))->where(new Zend_Db_Expr(
                    "DATE_FORMAT(`created_at`, '%y-%m-%d') < '".date('y-m-d', $time)."'"
                )
        );

        foreach ($collection as $customer){
            $this->_emailToCustomer($customer, 'regbirth');
        }
    }

    protected function _sendRegCoupon()
    {
        $days = intVal(Mage::getStoreConfig('ambirth/reg/days'));
        if ($days < 0)
            return;

        $collection = $this->_getCollection();
        $select = $collection->getSelect();
        $select->where(new Zend_Db_Expr(
            "DATE_FORMAT(created_at, '%Y-%m-%d') = '".date('Y-m-d', strtotime("-$days days"))."'"
        ));

        $this->_debug("Reg SQL: " . $collection->getSelect());

        foreach ($collection as $customer){
            $this->_emailToCustomer($customer, 'reg');
        }
    }

    protected function _sendActivityCoupon()
    {
        $days = intVal(Mage::getStoreConfig('ambirth/activity/days'));
        if ($days < 0)
            return;

        $resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core_read');

        $select = $db->select()
            ->from($resource->getTableName('log/customer'), array('customer_id'))
            ->having('MAX(login_at) < "'.date('Y-m-d', strtotime("-$days days")) .'"')
            ->group('customer_id')
            ->limit(1000);

        $this->_debug("Log-IN SQL(1): " . $select);

        $ids = $db->fetchCol($select);
        if (!$ids)
            return;

        $collection = $this->_getCollection()
            ->addFieldToFilter('entity_id', array('in'=>$ids));
        $collection->getSelect()->order('entity_id DESC');
        $collection->load();

        $this->_debug("Log-IN SQL(2): " . $collection->getSelect());

        foreach ($collection as $customer){
            $this->_emailToCustomer($customer, 'activity');
        }
    }

    protected function _sendWishlistCoupon()
    {
        $days = intVal(Mage::getStoreConfig('ambirth/wishlist/days'));
        if ($days < 0)
            return;

        $resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core_read');

        $select = $db->select()
            ->from(array('w'=>$resource->getTableName('wishlist/wishlist')), array('customer_id'))
            ->joinInner(array('i'=>$resource->getTableName('wishlist/item')), 'w.wishlist_id=i.wishlist_id', array())
            ->having('COUNT(i.product_id)>0 AND MAX(updated_at) <= "'.date('Y-m-d', strtotime("-$days days")) .'"')
            ->group('customer_id')
            ->limit(1000);

        $this->_debug("wishlist SQL(1): " . $select);

        $ids = $db->fetchCol($select);
        if (!$ids)
            return;

        $collection = $this->_getCollection()
            ->addFieldToFilter('entity_id', array('in'=>$ids));
        $collection->getSelect()->order('entity_id DESC');
        $collection->load();

        $this->_debug("Wishlist SQL(2): " . $collection->getSelect());

        foreach ($collection as $customer){
            $this->_emailToCustomer($customer, 'wishlist');
        }
    }

    /**
     * Observer event for order_placed action
     *
     * @param Varien_Event_Observer $observer
     */
    public function sendOrderPlacedCoupon(Varien_Event_Observer $observer) {
        $type = 'order_placed';
        if (Mage::getStoreConfig('ambirth/' . $type . '/enabled')) {
            $customer = $observer->getOrder()->getCustomer();
            try {
                if (!$customer->getId()) {
                    $customer = Mage::getModel('customer/customer');
                    $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                    $customer->setEmail($observer->getOrder()->getData('customer_email'));
                    $customer->setFirstname(Mage::helper('ambirth')->__('Dear Friend'));
                    $customer->setGroupId(0);
                    $customer->setId(0);
                }
            }catch(Exception $exc){
                Mage::log($exc->getMessage());
            }
            $this->_sendMailToCustomer($customer, $type);
        }
    }

    protected function _emailToCustomer($customer, $type)
    {
        if(!Mage::getStoreConfig('ambirth/' . $type . '/enabled', $customer->getStoreId())){
            return;
        }

        $groupId = $customer->getGroupId();
        $groups = Mage::getStoreConfig('ambirth/'.$type.'/customer_group', $customer->getStoreId());
        if(count($groups)){
            $groups = explode ( ',', $groups );
            if(!in_array($groupId,$groups)){
                return;
            }
        }

        $logCollection = Mage::getResourceModel('ambirth/log_collection')
            ->addFieldToFilter('type', $type)
            ->addFieldToFilter('customer_id', $customer->getId());

        if (in_array($type, array('birth', 'wishlist', 'activity')))
            $logCollection->addFieldToFilter('y', date('Y'));

        $this->_debug("CHECK SQL: " . $logCollection->getSelect());

        if ($logCollection->getSize() > 0)
            return;

        $this->_sendMailToCustomer($customer, $type);

        $logModel = Mage::getModel('ambirth/log')
            ->setY(date('Y'))
            ->setType($type)
            ->setCustomerId($customer->getId())
            ->setSentDate(date('Y-m-d H:i:s'));

        $logModel->save();
    }

    /**
     * Method for sending mail with coupon to customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param string $mailType
     */
    protected function _sendMailToCustomer($customer, $mailType) {
        try{
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);

            $store = Mage::app()->getStore($customer->getStoreId());
            $tpl = Mage::getModel('core/email_template');
            $data =  array(
                'website_name'  => $store->getWebsite()->getName(),
                'group_name'    => $store->getGroup()->getName(),
                'store_name'    => $store->getName(),
                'coupon'        => Mage::helper('ambirth')->generateCoupon($mailType, $store, $customer->getEmail()),
                'coupon_days'   => Mage::getStoreConfig('ambirth/'.$mailType.'/coupon_days', $store),
                'customer_name' => $customer->getName(),
            );

            if($mailType === "regbirth"){
                $year =  date('y', time()) - date('y', Mage::getModel('core/date')->timestamp($customer->getData('created_at')));
                $data['years_from_reg'] = $year;
            }

            $tpl->setDesignConfig(array('area'=>'frontend', 'store'=>$store->getId()))
                ->sendTransactional(
                    Mage::getStoreConfig('ambirth/' . $mailType . '/template', $store),
                    Mage::getStoreConfig('ambirth/general/identity', $store),
                    $customer->getEmail(),
                    $customer->getName(),
                    $data
                );
            $translate->setTranslateInline(true);
        }
        catch(Exception $exc){
            Mage::log($exc->getMessage());
        }

    }

    protected function _removeOldCoupons()
    {
        $days = intVal(Mage::getStoreConfig('ambirth/general/remove_days'));
        if ($days <= 0)
            return;

        $rules = Mage::getResourceModel('salesrule/rule_collection')
            ->addFieldToFilter('name', array('like'=>'Special Coupon%'))
            ->addFieldToFilter('from_date', array('lt' => date('Y-m-d', strtotime("-$days days"))))
        ;

        $errors = '';
        foreach ($rules as $rule){
            try {
                $rule->delete();
            }
            catch (Exception $e) {
                $errors .= "\r\nError when deleting rule #" . $rule->getId() . ' : ' . $e->getMessage();
            }
        }

        if ($errors)
            throw new Exception($errors);
    }

}
<?php

/*
* @copyright   Copyright ( c ) 2014 www.magebuzz.com
*/

class Magebuzz_Subscription_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{
	/**
	 * Subscribes by email
	 *
	 * @param string $email
	 * @throws Exception
	 * @return int
	 */
	public function subscribe($email)
	{
			$this->loadByEmail($email);
			$isSubscribed = false;
			$customerSession = Mage::getSingleton('customer/session');

			if(!$this->getId()) {
					$this->setSubscriberConfirmCode($this->randomSequence());
			}

			$isConfirmNeed   = (Mage::getStoreConfig(self::XML_PATH_CONFIRMATION_FLAG) == 1) ? true : false;
			$isOwnSubscribes = false;
			$ownerId = Mage::getModel('customer/customer')
					->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
					->loadByEmail($email)
					->getId();
			$isSubscribeOwnEmail = $customerSession->isLoggedIn() && $ownerId == $customerSession->getId();

			if (!$this->getId() || $this->getStatus() == self::STATUS_UNSUBSCRIBED
					|| $this->getStatus() == self::STATUS_NOT_ACTIVE
			) {
					if ($isConfirmNeed === true) {
							// if user subscribes own login email - confirmation is not needed
							$isOwnSubscribes = $isSubscribeOwnEmail;
							if ($isOwnSubscribes == true){
									$this->setStatus(self::STATUS_SUBSCRIBED);
							} else {
									$this->setStatus(self::STATUS_NOT_ACTIVE);
							}
					} else {
							$this->setStatus(self::STATUS_SUBSCRIBED);
					}
					$this->setSubscriberEmail($email);
			}else{
				$this->setStatus(self::STATUS_SUBSCRIBED);
				$isSubscribed = true;
			}

			if ($isSubscribeOwnEmail) {
					$this->setStoreId($customerSession->getCustomer()->getStoreId());
					$this->setCustomerId($customerSession->getCustomerId());
			} else {
					$this->setStoreId(Mage::app()->getStore()->getId());
					$this->setCustomerId(0);
			}

			$this->setIsStatusChanged(true);
			try {
					$this->save();
					$status = $this->getStatus();
					if ($isConfirmNeed === true
							&& $isOwnSubscribes === false
					) {
							$this->sendConfirmationRequestEmail();
					} else if($isSubscribed == false){
							$this->sendConfirmationSuccessEmail();
					}

					return $status;
			} catch (Exception $e) {
					throw new Exception($e->getMessage());
			}
	}
}
<?php
class Mgf_KSmartpay_Model_Resource_Transaction_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract{

	protected function _construce(){
		$this->_init('KSmartpay/transaction');
	}
}
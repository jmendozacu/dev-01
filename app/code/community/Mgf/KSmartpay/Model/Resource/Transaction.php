<?php
class Mgf_KSmartpay_Model_Resource_Transaction extends Mage_Core_Model_Resource_Db_Abstract{

	protected function _construct(){
		$this->_init('KSmartpay/transaction','entity_id');
	}
}
?>
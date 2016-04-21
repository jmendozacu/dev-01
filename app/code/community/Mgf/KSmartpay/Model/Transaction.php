<?php
class Mgf_KSmartpay_Model_Transaction extends Mage_Core_Model_Abstract{

	protected function _construct(){
		$this->_init('KSmartpay/transaction');
	}

	public function reGenerateOrderId($orderid){
		if($orderid){
			$reGenerate = str_replace(array('+','-'), '', filter_var($orderid,FILTER_SANITIZE_NUMBER_INT));
			if(!$this->load($orderid,'order_increment_id')->getId()){
				$this->setData('order_increment_id',$orderid)
					->setData('reference_number',$reGenerate)->save();
			}
			return $reGenerate;
		} else {
			return "00000";
		}
	}

}
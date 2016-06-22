<?php

class MarginFrame_Paysbuy_Helper_Data extends Mage_Payment_Helper_Data {
	
	public function debug($data , $exit = false){
		echo "<pre>";
		print_r($data);
		echo "</pre>";	
		if($exit){
			die("End Debug");
		}
	}
}

?>
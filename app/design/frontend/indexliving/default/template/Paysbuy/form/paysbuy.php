<?php

 	$data= $this->getQuoteData() ;
 
?>

<fieldset class="form-list">
    <?php $_code=$this->getMethodCode() ?>
    <ul id="payment_form_<?php echo $_code ?>" style="display:none">
        <li>
            <?php 
			$_checkouttext = $this->__('You will be redirected to Paysbuy Payment Gateway when you place an order.');
			$_checkouttext = "";
			if (Mage::getStoreConfig('payment/Paysbuy/checkouttext') !="") {
				$_checkouttext = Mage::getStoreConfig('payment/Paysbuy/checkouttext');
			}
			echo $_checkouttext;?><br />
        </li>
    </ul>	
</fieldset> 

<?php 
 class MarginFrame_Paysbuy_Block_Form_Redirect extends Mage_Core_Block_Abstract
{

    protected function _toHtml()
    {
		$Paysbuy = Mage::getModel('Paysbuy/method_paysbuy');
		$session = Mage::getSingleton('checkout/session');
		 $form = new Varien_Data_Form();
		// $form->setAction($Paysbuy->getPaysbuyUrl('?refid='.$session->getRefIdPaysbuy()))
		// ->setId('Paysbuy_checkout')
		// ->setName('sendform')
		// ->setMethod('GET')
		$form->setUseContainer(true);

		$_redirecttext = $this->__('You will be redirected to Paysbuy in a few seconds.<br/><br/>');
		$_redirecttext = "";
		if (Mage::getStoreConfig('payment/paysbuy/redirecttext') !="") {
				$_redirecttext = Mage::getStoreConfig('payment/paysbuy/redirecttext');
		}
		
		$_redirectfooter = $this->__('Copyright Paysbuy.com<br/><br/>');
		$_redirectfooter = "";
		if (Mage::getStoreConfig('payment/paysbuy/redirectfooter') !="") {
				$_redirectfooter = Mage::getStoreConfig('payment/paysbuy/redirectfooter');
		}
		
		
		$RedirectTime = 1;
		if (Mage::getStoreConfig('payment/paysbuy/redirecttime') !="") {
			$RedirectTime  = Mage::getStoreConfig('payment/paysbuy/redirecttime');
			if ((int)$RedirectTime<=0) {
				$RedirectTime = 1;
			}
		}
		
		$RedirectTime = $RedirectTime * 1000;
			
        $html = '<html>
				<body style="text-align:center;">';
		$html.= $_redirecttext . "<br/><br/>";
		$html.= $_redirectfooter; 

		$html.= $form->toHtml();
		// $html.= '<script type="text/javascript">
		// 			function formsubmit()
		// 			{
		// 			window.location = "'.$Paysbuy->getPaysbuyUrl('?refid='.$session->getRefIdPaysbuy()).'";
		// 			}
		// 			setTimeout("formsubmit()",  '. $RedirectTime .');

		// 		</script>';

        $html.= '</body></html>';

        return $html; 
    }
}

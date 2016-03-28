<?php 
 class MarginFrame_Paysbuy_Block_Form_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
		 $Paysbuy = Mage::getModel('Paysbuy/method_Paysbuy');

        $form = new Varien_Data_Form();
        $form->setAction($Paysbuy->getPaysbuyUrl())
            ->setId('Paysbuy_standard_checkout')
            ->setName('sendform')
            ->setMethod('post')
		    ->setUseContainer(true);
        foreach ($Paysbuy->getStandardCheckoutFormFields('redirect') as $field=>$value) {
           $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }
		
	
		$_redirecttext = $this->__('You will be redirected to Paysbuy in a few seconds.<br/><br/>');
		$_redirecttext = "";
		if (Mage::getStoreConfig('payment/Paysbuy/redirecttext') !="") {
				$_redirecttext = Mage::getStoreConfig('payment/Paysbuy/redirecttext');
		}
		
		$_redirectfooter = $this->__('Copyright Paysbuy.com<br/><br/>');
		$_redirectfooter = "";
		if (Mage::getStoreConfig('payment/Paysbuy/redirectfooter') !="") {
				$_redirectfooter = Mage::getStoreConfig('payment/Paysbuy/redirectfooter');
		}
		
		$RedirectTime = 1;
		if (Mage::getStoreConfig('payment/Paysbuy/redirecttime') !="") {
			$RedirectTime  = Mage::getStoreConfig('payment/Paysbuy/redirecttime');
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
      //  $html.= '<script type="text/javascript">
	   		// 	  function formsubmit()
				  // {
				  // 	document.sendform.submit();	
				  // }
				  // setTimeout("formsubmit()",  '. $RedirectTime .');
	     //        </script>';
	  
        $html.= '</body></html>';

        return $html; 
    }
}

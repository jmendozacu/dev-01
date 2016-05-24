<?php

class MarginFrame_Sync_Helper_Data extends Mage_Core_Helper_Abstract
{
	
    /*
    public function sendEmail($subject, $body, $toEmailList = '', $from_name = '')
    {
        if(empty($toEmailList)) return false;

        $message = array (
            'subject'       => $subject,
            'html'          => $body,
            'from_email'    => 'no-reply@cdiscount.co.th',
            'from_name'     => 'Sync Monitor',
            'to_email'      => array_values($toEmailList),
            'to_name'       => array_values($toEmailList),
        );

        if(!empty($from_name)) {
            $message['from_name']  = $from_name;
        }

        try {
            $mandrill = Mage::helper('mandrill')->api();
            $mandrill->setApiKey(Mage::helper('mandrill')->getApiKey());
            $mandrill->sendEmail($message);
            if ($mandrill->errorCode) {
                echo "sendEmail() error: \$mandrill->errorCode = [{$mandrill->errorCode}]\n";
            }
        } catch (Exception $e) {
            echo "sendEmail() error: [{$e->getMessage()}]\n";
        }
    }
    */
	
}

?>
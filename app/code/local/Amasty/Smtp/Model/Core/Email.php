<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
class Amasty_Smtp_Model_Core_Email extends Mage_Core_Model_Email
{
    public function send()
    {
        if (!Mage::getStoreConfig('amsmtp/general/enable'))
        {
            return parent::send();
        }

        if (Mage::getStoreConfigFlag('system/smtp/disable')) {
            return $this;
        }

        Mage::helper('amsmtp')->debug('Ready to send e-mail at amsmtp/core_email::send()');

        $mail = new Zend_Mail();

        $mail->setMessageId(true);

        if (strtolower($this->getType()) == 'html') {
            $mail->setBodyHtml($this->getBody());
        }
        else {
            $mail->setBodyText($this->getBody());
        }

        $mail->setFrom($this->getFromEmail(), $this->getFromName())
             ->addTo($this->getToEmail(), $this->getToName())
             ->setSubject($this->getSubject());

        $logId = Mage::helper('amsmtp')->log(array(
            'subject'           => $this->getSubject(),
            'body'              => $this->getBody(),
            'recipient_name'    => $this->getToName(),
            'recipient_email'   => $this->getToEmail(),
            'template_code'     => 'none',
            'status'            => Amasty_Smtp_Model_Log::STATUS_PENDING,
        ));

        try
        {
            $transportFacade = Mage::getModel('amsmtp/transport');

            if (!Mage::getStoreConfig('amsmtp/general/disable_delivery'))
            {
                $mail->send($transportFacade->getTransport());
            } else
            {
                Mage::helper('amsmtp')->debug('Actual delivery disabled under settings.');
            }

            Mage::helper('amsmtp')->logStatusUpdate($logId, Amasty_Smtp_Model_Log::STATUS_SENT);
            Mage::helper('amsmtp')->debug('E-mail sent successfully at amsmtp/core_email::send().');
        } catch (Exception $e)
        {
            Mage::helper('amsmtp')->logStatusUpdate($logId, Amasty_Smtp_Model_Log::STATUS_FAILED);
            Mage::helper('amsmtp')->debug('Error sending e-mail: ' . $e->getMessage());
        }

        return $this;
    }
}
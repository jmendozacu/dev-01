<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */


class Amasty_Smtp_Model_Core_Email_Queue extends Mage_Core_Model_Email_Queue
{
    public function send()
    {
        if (!Mage::getStoreConfig('amsmtp/general/enable'))
        {
            return parent::send();
        }


        /** @var $collection Mage_Core_Model_Resource_Email_Queue_Collection */
        $collection = Mage::getModel('core/email_queue')->getCollection()
            ->addOnlyForSendingFilter()
            ->setPageSize(self::MESSAGES_LIMIT_PER_CRON_RUN)
            ->setCurPage(1)
            ->load();

        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

        /** @var $message Mage_Core_Model_Email_Queue */
        foreach ($collection as $message) {
            Mage::helper('amsmtp')->debug('Ready to send e-mail at amsmtp/core_email_queue::send()');
            if ($message->getId()) {
                $parameters = new Varien_Object($message->getMessageParameters());
                if ($parameters->getReturnPathEmail() !== null) {
                    $mailTransport = new Zend_Mail_Transport_Sendmail("-f" . $parameters->getReturnPathEmail());
                    Zend_Mail::setDefaultTransport($mailTransport);
                }

                $mailer = new Zend_Mail('utf-8');

                $mailer->setMessageId(true);

                foreach ($message->getRecipients() as $recipient) {
                    list($email, $name, $type) = $recipient;
                    switch ($type) {
                        case self::EMAIL_TYPE_BCC:
                            $mailer->addBcc($email, '=?utf-8?B?' . base64_encode($name) . '?=');
                            break;
                        case self::EMAIL_TYPE_TO:
                        case self::EMAIL_TYPE_CC:
                        default:
                        $mailer->addTo($email, '=?utf-8?B?' . base64_encode($name) . '?=');
                        break;
                    }
                }

                $text = $message->getMessageBody();
                $plainText = Mage::helper('amsmtp')->html2text($text);

                if ($parameters->getIsPlain()) {
                    $mailer->setBodyText($text);
                } else {
                    $mailer->setBodyText($plainText);
                    $mailer->setBodyHtml($text);
                }

                $mailer->setSubject('=?utf-8?B?' . base64_encode($parameters->getSubject()) . '?=');
                $mailer->setFrom($parameters->getFromEmail(), $parameters->getFromName());
                if ($parameters->getReplyTo() !== null) {
                    $mailer->setReplyTo($parameters->getReplyTo());
                }
                if ($parameters->getReturnTo() !== null) {
                    $mailer->setReturnPath($parameters->getReturnTo());
                }

                $messageParameters = $message->getMessageParameters();

                $logId = Mage::helper('amsmtp')->log(array(
                    'subject'           => $messageParameters['subject'],
                    'body'              => $message->getMessageBody(),
                    'recipient_name'    => $message->_recipients[0][1],
                    'recipient_email'   => $message->_recipients[0][0],
                    'template_code'     => 'none',
                    'status'            => Amasty_Smtp_Model_Log::STATUS_PENDING,
                ));

                try {
                    if (!Mage::getStoreConfig('amsmtp/general/disable_delivery'))
                    {
                        $transportFacade = Mage::getModel('amsmtp/transport');
                        $mailer->send($transportFacade->getTransport());
                    } else
                    {
                        Mage::helper('amsmtp')->debug('Actual delivery disabled under settings.');
                    }
                    unset($mailer);
                    $message->setProcessedAt(Varien_Date::formatDate(true));
                    $message->save();
                    Mage::helper('amsmtp')->logStatusUpdate($logId, Amasty_Smtp_Model_Log::STATUS_SENT);
                    Mage::helper('amsmtp')->debug('E-mail sent successfully at amsmtp/core_email_queue::send().');
                }
                catch (Exception $e) {
                    Mage::helper('amsmtp')->logStatusUpdate($logId, Amasty_Smtp_Model_Log::STATUS_FAILED);
                    Mage::helper('amsmtp')->debug('Error sending e-mail: ' . $e->getMessage());
                    unset($mailer);
                    $oldDevMode = Mage::getIsDeveloperMode();
                    Mage::setIsDeveloperMode(true);
                    Mage::logException($e);
                    Mage::setIsDeveloperMode($oldDevMode);

                    return false;
                }
            }

        }

        return $this;
    }
}

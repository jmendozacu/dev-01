<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
class Amasty_Smtp_Block_Adminhtml_View_Message extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amasty/amsmtp/view.phtml');
    }

    public function getMessage()
    {
        return Mage::registry('amsmtp_log');
    }

    public function getDate()
    {
        $date = Mage::app()->getLocale()->date($this->getMessage()->getCdate());
        return $date;
    }

    public function getTo()
    {
        $to = $this->getMessage()->getRecipientName() . ' &lt;' . $this->getMessage()->getRecipientEmail() . '&gt;';
        return $to;
    }

    public function getSubject()
    {
        return $this->getMessage()->getSubject();
    }

    public function getBody()
    {
        $body = $this->getMessage()->getBody();
        $body = preg_replace("/<style\\b[^>]*>(.*?)<\\/style>/s", "", $body);
        $body = preg_replace("/<body\\b[^>]*>/s", "", $body);
        $body = str_replace('</body>', '', $body);
        return $body;
    }
}
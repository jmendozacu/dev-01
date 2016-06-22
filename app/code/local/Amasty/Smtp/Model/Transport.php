<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */
class Amasty_Smtp_Model_Transport extends Varien_Object
{
    protected $_transport = null;

    public function getTransport($params = array())
    {
        if (null == $this->_transport)
        {
            $config = $this->_getConfig($params);
            $host = $config['host'];
            $this->_transport = new Zend_Mail_Transport_Smtp($host, $config);
        }
        return $this->_transport;
    }

    public function runTest($params = array())
    {
        $config = $this->_getConfig($params);
        $sock = false;
        try
        {
            $sock = fsockopen($config['host'], $config['port'], $errno, $errstr, 20);
        } catch (Exception $e) {}
        if ($sock)
        {
            Mage::helper('amsmtp')->debug('Connection test successful: connected to ' . $config['host'] . ':' . $config['port']);
            if ($config['test_email'])
            {
                $from = Mage::getStoreConfig('trans_email/ident_general/email');
                Mage::helper('amsmtp')->debug('Preparing to send test e-mail to ' . $config['test_email'] . ' from ' . $from);
                $mail = new Zend_Mail();
                $mail->addTo($config['test_email'])
                     ->setMessageId(true)
                     ->setSubject(Mage::helper('amsmtp')->__('Amasty SMTP Email Test Message'))
                     ->setBodyText(Mage::helper('amsmtp')->__('If you see this e-mail, your configuration is OK.'))
                     ->setFrom($from);
                try
                {
                    $mail->send($this->getTransport($params));
                    Mage::helper('amsmtp')->debug('Test e-mail was sent successfully!');
                } catch (Exception $e)
                {
                    Mage::helper('amsmtp')->debug('Test e-mail failed: ' . $e->getMessage());
                    return false;
                }
            }
            return true;
        }
        Mage::helper('amsmtp')->debug('Connection test failed: connection to ' . $config['host'] . ':' . $config['port'] . ' failed. Error: ' . $errstr . ' (' . $errno . ')');
        return false;
    }

    protected function _getConfig($params = array())
    {
        $config = array();
        $host   = (isset($params['server']) && $params['server'])
            ? $params['server'] : Mage::getStoreConfig('amsmtp/smtp/server');

        $config['host'] = $host;

        $config['port']     = (isset($params['port']) && $params['port'])
            ? $params['port'] : Mage::getStoreConfig('amsmtp/smtp/port');

        if (   ($params && isset($params['auth']) && $params['auth'])
            || (!$params && !isset($params['auth']) && Mage::getStoreConfig('amsmtp/smtp/auth'))    )
        {
            $config['auth']     = (isset($params['auth']) && $params['auth'])
                ? $params['auth'] : Mage::getStoreConfig('amsmtp/smtp/auth');
            $config['username'] = (isset($params['login']) && $params['login'])
                ? $params['login'] : Mage::getStoreConfig('amsmtp/smtp/login');
            $config['password'] = (isset($params['passw']) && $params['passw'])
                ? $params['passw'] : Mage::getStoreConfig('amsmtp/smtp/passw');
        }

        $config['ssl']     = (isset($params['security']) && $params['security'])
                                    ? $params['security'] : Mage::getStoreConfig('amsmtp/smtp/sec');

        if (!$config['ssl'])
        {
            unset($config['ssl']); // if empty, we do not need to pass the value at all
        }

        $config['test_email']     = (isset($params['test_email']) && $params['test_email'])
                            ? $params['test_email'] : Mage::getStoreConfig('amsmtp/smtp/test_email');

        $debugConfig = $config;
        if (isset($debugConfig['password']) && $debugConfig['password'])
        {
            $debugConfig['password'] = '***';
            unset($debugConfig['test_email']);
        }
        Mage::helper('amsmtp')->debug('Config requested: ' . print_r($debugConfig, true));

        return $config;
    }
}
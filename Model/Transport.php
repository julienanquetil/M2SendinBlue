<?php

namespace JulienAnquetil\M2SendinBlue\Model;

class Transport extends \Zend_Mail_Transport_Smtp implements \Magento\Framework\Mail\TransportInterface
{
    /**
     * @var \Magento\Framework\Mail\MessageInterface
     */
    protected $_message;

    /**
     * @param MessageInterface $message
     * @param null $parameters
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Magento\Framework\Mail\MessageInterface $message,
        \Magento\Framework\ObjectManagerInterface $objectmanager
    )

    {
        $helper = $objectmanager->create('JulienAnquetil\M2SendinBlue\Helper\Data');

        if (!$message instanceof \Zend_Mail) {
            throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
        }
        $smtpHost= $helper->getGeneralConfig('smtp_host');
        $smtpConf = [
            'auth' => 'login',//auth type
            'ssl' => 'tls',
            'port' => $helper->getGeneralConfig('smtp_port'),
            'username' => $helper->getGeneralConfig('smtp_login'),
            'password' => $helper->getGeneralConfig('smtp_pass'),
        ];

        parent::__construct($smtpHost, $smtpConf);
        $this->_message = $message;
    }

    /**
     * Send a mail using this transport
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage()
    {
        try {
            parent::send($this->_message);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($e->getMessage()), $e);
        }
    }
}
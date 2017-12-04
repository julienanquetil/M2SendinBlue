<?php

namespace JulienAnquetil\M2SendinBlue\Model;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Exception\MailException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;

class Transport extends \Zend_Mail_Transport_Smtp implements TransportInterface
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
        MessageInterface $message,
        ObjectManagerInterface $objectmanager
    ) {

        if (!$message instanceof \Zend_Mail) {
            throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
        }

        $helper = $objectmanager->create('JulienAnquetil\M2SendinBlue\Helper\Data');

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
            throw new MailException(new Phrase($e->getMessage()), $e);
        }
    }
    
    /**
     * Get message
     *
     * @return \Magento\Framework\Mail\MessageInterface
     * @since 100.2.0
     */
    public function getMessage()
    {
        return $this->message;
    }
}

<?php

namespace JulienAnquetil\M2SendinBlue\Observer;

use JulienAnquetil\M2SendinBlue\Model\SendinBlue;
use Magento\Framework\Event\ObserverInterface;

/**
 * Customer login observer
 */
class CheckSync implements ObserverInterface
{

    /**
     * Message manager
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $_subscriber;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor
     *
     * @param  \Magento\Framework\Message\ManagerInterface $messageManager Message Manager
     * @param  \Magento\Newsletter\Model\Subscriber $subscriber
     * @param  \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param  \Magento\Framework\ObjectManagerInterface $objectmanager
     *
     * @return void
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\ObjectManagerInterface $objectmanager
    )
    {
        $this->messageManager = $messageManager;
        $this->_subscriber = $subscriber;
        $this->scopeConfig = $scopeConfig;
        $this->_objectManager = $objectmanager;
    }

    /**
     * Display a custom message when customer log in
     *
     * @param  \Magento\Framework\Event\Observer $observer Observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $customer = $event->getCustomer();
        $customerEmail = $customer->getEmail();
        $customerName = $customer->getFirstname();
        $customerLastname = $customer->getLastname();

        $checkSubscriber = $this->_subscriber->loadByEmail($customerEmail);

        if ($checkSubscriber->isSubscribed()) {
            // Customer is subscribed
            $is_subscribed = 'true';

            //sync content with Sendinblue
            $helper = $this->_objectManager->create('JulienAnquetil\M2SendinBlue\Helper\Data');
            $apikey = $helper->getGeneralConfig('api_key');
            $listId = $helper->getGeneralConfig('list_id');
            if (isset ($apikey) && isset($listId)) {
                //connect to API
                $mailerApi = new SendinBlue('https://api.sendinblue.com/v2.0', $apikey, '5000');
                $data = array( "email" => $customerEmail,
                    "attributes" => array("NOM"=>$customerName, "PRENOM"=>$customerLastname),
                    "listid" => [$listId],
                );
                $result = $mailerApi->create_update_user($data);

            }

        } else {
            // Customer is not subscribed
            $is_subscribed = 'false';
        }

        $this->messageManager->addSuccessMessage(__('Welcome back beloved customer %1 !', $customer->getCustomer()));
    }


}
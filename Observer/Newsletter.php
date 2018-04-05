<?php

namespace JulienAnquetil\M2SendinBlue\Observer;

use JulienAnquetil\M2SendinBlue\Model\SendinBlue;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Newsletter\Model\Subscriber;
use Magento\Framework\ObjectManagerInterface;

class Newsletter implements ObserverInterface
{

    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $subscriber;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor
     *
     * @param  \Magento\Newsletter\Model\Subscriber $subscriber
     * @param  \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param  \Magento\Framework\ObjectManagerInterface $objectmanager
     *
     */
    public function __construct(
        Subscriber $subscriber,
        ScopeConfigInterface $scopeConfig,
        ObjectManagerInterface $objectmanager
    )
    {
        $this->subscriber = $subscriber;
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
    public function execute(Observer $observer)
    {

        $event = $observer->getEvent();
        $customer = $event->getSubscriber();
        $customerEmail = $customer->getSubscriberEmail();
        $customerName = $customer->getFirstname();
        $customerLastname = $customer->getLastname();
        $checkSubscriber = $this->subscriber->loadByEmail($customerEmail);

        if ($checkSubscriber->isSubscribed()) {
            // Customer is subscribed
            //sync content with Sendinblue
            $helper = $this->_objectManager->create('JulienAnquetil\M2SendinBlue\Helper\Data');
            $apikey = $helper->getGeneralConfig('api_key');
            $listId = $helper->getGeneralConfig('list_id');
            if (isset($apikey) && isset($listId)) {
                //connect to API
                $mailerApi = new SendinBlue('https://api.sendinblue.com/v2.0', $apikey, '5000');
                $data = ["email" => $customerEmail,
                        "attributes" =>
                        [
                            "NOM" => $customerName,
                            "PRENOM" => $customerLastname
                        ],
                        "listid" => [$listId],
                ];
                $mailerApi->create_update_user($data);
            }
        }
    }
}

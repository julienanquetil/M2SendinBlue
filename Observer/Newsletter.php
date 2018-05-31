<?php
/**
 * *
 * M2SendinBlue
 *
 * @author      Julien Anquetil (https://www.julien-anquetil.com/)
 * @copyright   Copyright 2018 Julien ANQUETIL (https://www.julien-anquetil.com/)
 * @license     http://opensource.org/licenses/MIT MIT
 *
 *
 */

namespace JulienAnquetil\M2SendinBlue\Observer;

use JulienAnquetil\M2SendinBlue\Model\SendinBlue;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Newsletter\Model\Subscriber;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;

class Newsletter implements ObserverInterface
{

    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    private $subscriber;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Newsletter constructor.
     * @param Subscriber $subscriber
     * @param ScopeConfigInterface $scopeConfig
     * @param ObjectManagerInterface $objectmanager
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        Subscriber $subscriber,
        ScopeConfigInterface $scopeConfig,
        ObjectManagerInterface $objectmanager,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->subscriber = $subscriber;
        $this->scopeConfig = $scopeConfig;
        $this->objectManager = $objectmanager;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
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
        $storeId = $this->storeManager->getStore()->getWebsiteId();

        if ($checkSubscriber->isSubscribed()) {
            // Customer is subscribed
            //sync content with Sendinblue
            $helper = $this->objectManager->create('JulienAnquetil\M2SendinBlue\Helper\Data');
            $apikey = $helper->getGeneralConfig('api_key', $storeId);
            $listId = $helper->getGeneralConfig('list_id', $storeId);
            if (isset($apikey) && isset($listId)) {
                try {
                    //connect to API
                    $mailerApi = new SendinBlue('https://api.sendinblue.com/v2.0', $apikey, '5000');
                    $data = [ "email" => $customerEmail,
                        "attributes" => ["NOM"=>$customerName, "PRENOM"=>$customerLastname],
                        "listid" => [$listId],
                    ];
                    $mailerApi->create_update_user($data);
                } catch(\Exception $e) {
                    $this->logger->addError($e->getMessage());
                }
            }
        }
    }
}

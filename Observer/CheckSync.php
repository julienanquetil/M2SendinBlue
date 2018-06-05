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
use JulienAnquetil\M2SendinBlue\Helper\Data;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Newsletter\Model\Subscriber;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Psr\Log\LoggerInterface;

/**
 * Customer login observer
 */
class CheckSync implements ObserverInterface
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
     * @var \Magento\Customer\Model\Session $customerSession
     */
    private $customerSession;

    /**
     * @var LoggerInterface|\Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Data
     */
    private $helper;

    /**
     * CheckSync constructor.
     * @param Subscriber $subscriber
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param Data $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Subscriber $subscriber,
        ScopeConfigInterface $scopeConfig,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        Data $helper,
        LoggerInterface $logger
    ) {
        $this->subscriber = $subscriber;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * Display a custom message when customer log in
     *
     * @param Observer|\Magento\Framework\Event\Observer $observer Observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $customer = $this->customerSession->getCustomer();
        $customerEmail = $customer->getEmail();
        $customerName = $customer->getFirstname();
        $customerLastname = $customer->getLastname();
        $storeId = $this->storeManager->getStore()->getWebsiteId();

        $checkSubscriber = $this->subscriber->loadByEmail($customerEmail);
        if ($checkSubscriber->isSubscribed()) {
            // Customer is subscribed
            //sync content with Sendinblue
            $apikey = $this->helper->getApiKey($storeId);
            $listId = $this->helper->getListId($storeId);
            if (isset($apikey) && isset($listId)) {
                try {
                    //connect to API
                    $mailerApi = new SendinBlue('https://api.sendinblue.com/v2.0', $apikey, '5000');
                    $data = [ "email" => $customerEmail,
                        "attributes" => ["NOM"=>$customerName, "PRENOM"=>$customerLastname],
                        "listid" => [$listId],
                    ];
                    $mailerApi->create_update_user($data);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }
    }
}

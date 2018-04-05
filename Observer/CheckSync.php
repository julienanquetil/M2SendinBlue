<?php

namespace JulienAnquetil\M2SendinBlue\Observer;

use JulienAnquetil\M2SendinBlue\Model\SendinBlue;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Newsletter\Model\Subscriber;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Customer login observer
 */
class CheckSync implements ObserverInterface
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
     * @var \Magento\Customer\Model\Session $customerSession
     */
    protected $customerSession;

    /**
     * Constructor
     *
     * @param ManagerInterface|\Magento\Framework\Message\ManagerInterface $messageManager Message Manager
     * @param Subscriber|\Magento\Newsletter\Model\Subscriber $subscriber
     * @param ScopeConfigInterface|\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param ObjectManagerInterface|\Magento\Framework\ObjectManagerInterface $objectmanager
     *
     */
    public function __construct(
        Subscriber $subscriber,
        ScopeConfigInterface $scopeConfig,
        ObjectManagerInterface $objectmanager,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->subscriber = $subscriber;
        $this->scopeConfig = $scopeConfig;
        $this->objectManager = $objectmanager;
        $this->_customerSession = $customerSession;
    }

    /**
     * Display a custom message when customer log in
     *
     * @param Observer|\Magento\Framework\Event\Observer $observer Observer
     * @return void
     */
     public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $this->_customerSession->getCustomer();
        $customerEmail = $customer->getEmail();
        $customerName = $customer->getFirstname();
        $customerLastname = $customer->getLastname();

        $checkSubscriber = $this->subscriber->loadByEmail($customerEmail);
        if ($checkSubscriber->isSubscribed()) {
            // Customer is subscribed
            //sync content with Sendinblue
            $helper = $this->objectManager->create('JulienAnquetil\M2SendinBlue\Helper\Data');
            $apikey = $helper->getGeneralConfig('api_key');
            $listId = $helper->getGeneralConfig('list_id');
            if (isset($apikey) && isset($listId)) {
                //connect to API
                $mailerApi = new SendinBlue('https://api.sendinblue.com/v2.0', $apikey, '5000');
                $data = [ "email" => $customerEmail,
                    "attributes" => ["NOM"=>$customerName, "PRENOM"=>$customerLastname],
                    "listid" => [$listId],
                ];
                $mailerApi->create_update_user($data);
            }
        }
    }
}

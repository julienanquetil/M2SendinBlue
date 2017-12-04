<?php

namespace JulienAnquetil\M2SendinBlue\Observer;

use JulienAnquetil\M2SendinBlue\Model\SendinBlue;
use JulienAnquetil\M2SendinBlue\Model\SendinBlueAutomation;
use Magento\Framework\Event\ObserverInterface;

class AfterPlaceOrder implements ObserverInterface
{
    /**
     * Order Model
     *
     * @var \Magento\Sales\Model\Order $order
     */
    protected $order;

    /**
     * Constructor
     *
     * @param  \Magento\Sales\Model\Order $order
     * @param  \Magento\Framework\ObjectManagerInterface $objectmanager
     *
     * @return void
     */
    public function __construct(
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\ObjectManagerInterface $objectmanager
    ) {
    
        $this->order = $order;
        $this->_objectManager = $objectmanager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderId = $observer->getEvent()->getOrderIds();
        $order = $this->order->load($orderId);
        $orderTotal = $order->getGrandTotal();

        //get Order All Item
        $itemCollection = $order->getItemsCollection();
        $customer = $order->getCustomerId(); // using this id you can get customer name

        $helper = $this->_objectManager->create('JulienAnquetil\M2SendinBlue\Helper\Data');
        $apikeyAutomation = $helper->getGeneralConfig('automation_api_key');

        $event = new SendinBlueAutomation($apikeyAutomation);
        $data = array();
        $data['name'] = 'order_succes';
        $data['order_id'] = $orderId;
        $data['amount'] = $orderTotal;
        $event->track($data);
    }
}

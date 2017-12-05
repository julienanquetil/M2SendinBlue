<?php

namespace JulienAnquetil\M2SendinBlue\Observer;

use JulienAnquetil\M2SendinBlue\Model\SendinBlueAutomation;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Event\Observer;

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
     */
    public function __construct(
        Order $order,
        ObjectManagerInterface $objectmanager
    ) {
    
        $this->order = $order;
        $this->_objectManager = $objectmanager;
    }

    public function execute(Observer $observer)
    {
        $orderId = $observer->getEvent()->getOrderIds();
        $order = $this->order->load($orderId);
        $orderTotal = $order->getGrandTotal();

        //get Order All Item
        //$itemCollection = $order->getItemsCollection();
        //$customer = $order->getCustomerId(); // using this id you can get customer name

        $helper = $this->_objectManager->create('JulienAnquetil\M2SendinBlue\Helper\Data');
        $apikeyAutomation = $helper->getGeneralConfig('automation_api_key');

        $event = new SendinBlueAutomation($apikeyAutomation);
        $data = [];
        $data['name'] = 'order_succes';
        $data['order_id'] = $orderId;
        $data['amount'] = $orderTotal;
        $event->track($data);
    }
}

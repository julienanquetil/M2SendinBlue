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

namespace JulienAnquetil\M2SendinBlue\Model\Config\Source;

use JulienAnquetil\M2SendinBlue\Model\SendinBlue;
use JulienAnquetil\M2SendinBlue\Helper\Data;
use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;

class Listes implements ArrayInterface
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var Data
     */
    private $helper;

    /**
     * Listes constructor.
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     * @param Data $helper
     */
    public function __construct(
        RequestInterface $request,
        LoggerInterface $logger,
        Data $helper
    ) {
        $this->request = $request;
        $this->logger = $logger;
        $this->helper = $helper;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function toOptionArray()
    {

        $storeid = (int)$this->request->getParam('store');
        $apikey = $this->helper->getApiKey($storeid);

        if (isset($apikey)) {
            /* connect to API */
            try {
                //connect to API
                $mailerApi = new SendinBlue('https://api.sendinblue.com/v2.0', $apikey, '5000');
                /* get all list */
                $data = [
                    "page" => 1,
                    "page_limit" => 50
                ];
                $result = $mailerApi->get_lists($data);
                $returnList = [];
                if ('success' === $result["code"]) {
                    foreach ($result as $key => $values) {
                        if ($key == 'data') {
                            foreach ($values as $index => $listes) {
                                if ('lists' === $index) {
                                    foreach ($listes as $liste) {
                                        $returnList[] = ['value' => $liste["id"], 'label' => $liste["name"]];
                                    }
                                }
                            }
                        }
                    }
                    // retour des listes
                    return $returnList;
                } else {
                    /* ERROR */
                    $this->logger->addError('Unable to retrieve Sendinblue Contact List');
                }
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        } else {
            /* format return */
            return [
                ['value' => 0, 'label' => __('Sendinblue Contact List')]
            ];
        }
    }
}

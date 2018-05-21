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

use Magento\Framework\ObjectManagerInterface;
use JulienAnquetil\M2SendinBlue\Model\SendinBlue;
use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\App\RequestInterface;

class Listes implements ArrayInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    
    /**
    * @var \Magento\Framework\App\RequestInterface
    */
    protected $request;

    /**
     * Constructor.
     * @param ObjectManagerInterface $objectmanager
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        ObjectManagerInterface $objectmanager,
        RequestInterface $request,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->objectManager = $objectmanager;
        $this->request = $request;
        $this->_logger = $logger;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function toOptionArray()
    {

        $helper = $this->objectManager->create('JulienAnquetil\M2SendinBlue\Helper\Data');
        $storeid = (int) $request->getParam('store');
        $apikey = $helper->getGeneralConfig('api_key',$storeid);

        if (isset($apikey)) {
            /* connect to API */
            try{
                //connect to API
                $mailerApi = new SendinBlue('https://api.sendinblue.com/v2.0', $apikey, '5000');
                /* get all list */
                $data = [
                    "page" => 1,
                    "page_limit" => 50
                ];
                $result = $mailerApi->get_lists($data);
                $returnList =[];
                if ('success' === $result["code"]) {
                    foreach ($result as $key => $values) {
                        if ($key == 'data') {
                            foreach ($values as $index => $listes) {
                                if ('lists' === $index) {
                                    foreach ($listes as $liste) {
                                        $returnList[] = ['value' => $liste["id"],'label'=> $liste["name"]];
                                    }
                                }
                            }
                        }
                    }
                    // retour des listes
                    return $returnList;
                } else {
                    /* ERROR */
                    throw new \Exception('Unable to retrieve Sendinblue Contact List');
                }
            }
            catch(\Exception $e){
                $this->_logger->addError($e->getMessage());
            }
        } else {
            /* format return */
            return [
                ['value' => 0, 'label' => __('Sendinblue Contact List')]
            ];
        }
    }
}

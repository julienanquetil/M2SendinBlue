<?php

namespace JulienAnquetil\M2SendinBlue\Model\Config\Source;

use Magento\Framework\ObjectManagerInterface;
use JulienAnquetil\M2SendinBlue\Model\SendinBlue;
use Magento\Framework\Option\ArrayInterface;

class Listes implements ArrayInterface
{

    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectmanager
    ) {
        $this->objectManager = $objectmanager;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function toOptionArray()
    {

        $helper = $this->objectManager->create('JulienAnquetil\M2SendinBlue\Helper\Data');
        $apikey = $helper->getGeneralConfig('api_key');

        if (isset($apikey)) {
            /* connect to API */
            $mailerApi = new SendinBlue('https://api.sendinblue.com/v2.0', $apikey, '5000');
            /* @TODO : verif si url ok */
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
        } else {
            /* format return */
            return [
                ['value' => 0, 'label' => __('Sendinblue Contact List')]
            ];
        }
    }
}

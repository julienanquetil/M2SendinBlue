<?php

namespace JulienAnquetil\M2SendinBlue\Model\Config\Source;

use JulienAnquetil\M2SendinBlue\Model\SendinBlue;

class Listes implements \Magento\Framework\Option\ArrayInterface
{

    private $_objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager
    )
    {
        $this->_objectManager = $objectmanager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {

        $helper = $this->_objectManager->create('JulienAnquetil\M2SendinBlue\Helper\Data');
        $apikey = $helper->getGeneralConfig('api_key');

        if (isset ($apikey)) {
            /* connect to API */
            $mailerApi = new SendinBlue('https://api.sendinblue.com/v2.0', $apikey, '5000');
            /* @TODO : verif si url ok */
            /* get all list */
            $data = array(
                "page" => 1,
                "page_limit" => 50
            );
            $result = $mailerApi->get_lists($data);
            $returnList =[];
            if ('success' === $result["code"]) {
                foreach ($result as $key => $values) {
                    if ($key == 'data') {
                        foreach ($values as $index => $listes){
                            if ('lists' === $index) {
                                foreach ($listes as $liste){
                                    $returnList[] = array('value' => $liste["id"],'label'=> $liste["name"]);
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
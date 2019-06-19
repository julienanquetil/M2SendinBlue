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

namespace JulienAnquetil\M2SendinBlue\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
{
    const XML_PATH_SENDINBLUE = 'config/sendinblue/';
    const XML_PATH_APIKEY = 'config/sendinblue/api_key';
    const XML_PATH_LISTID = 'config/sendinblue/list_id';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Data constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Context $context
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Context $context
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @param string $field
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $code
     * @param null $storeId
     * @return mixed
     */
    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SENDINBLUE . $code, $storeId);
    }

    /**
     * @param string $field
     * @return mixed
     */
    public function getStoreConfig($field)
    {
        return $this->scopeConfig->getValue(
            $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param int $store
     * @return mixed
     */
    public function getApiKey($store = null)
    {
        return $this->getConfigValue(self::XML_PATH_APIKEY, $store);
    }

    /**
     * @param int $store
     * @return mixed
     */
    public function getListId($store = null)
    {
        return $this->getConfigValue(self::XML_PATH_LISTID, $store);
    }
}

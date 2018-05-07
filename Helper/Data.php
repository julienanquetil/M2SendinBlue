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

class Data extends AbstractHelper
{
    protected $storeManager;
    protected $objectManager;

    const XML_PATH_SENDINBLUE = 'config/sendinblue/';

    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SENDINBLUE . $code, $storeId);
    }

    public function getStoreConfig($field)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE
        );
    }
}

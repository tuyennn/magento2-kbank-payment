<?php

namespace GhoSter\KbankPayments\Gateway;

use GhoSter\KbankPayments\Model\Adminhtml\Source\Environment;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;

class Config extends \Magento\Payment\Gateway\Config\Config
{
    public const METHOD_KBANK_INLINE = 'kbank_inline';
    public const METHOD_KBANK_EMBEDDED_INSTALLMENT = 'kbank_embedded_installment';
    public const METHOD_KBANK_EMBEDDED_FULLPAYMENT = 'kbank_embedded_fullpayment';

    protected const KEY_ACTIVATE = 'active';
    protected const KEY_PUBLIC_KEY = 'public_key';
    protected const KEY_SECRET_KEY = 'secret_key';
    protected const KEY_DEBUG = 'debug';
    protected const KEY_SHOP_NAME = 'shop_name';
    protected const KEY_ENVIRONMENT = 'environment';
    protected const ENDPOINT_URL_SANDBOX = 'https://dev-kpaymentgateway-services.kasikornbank.com';
    protected const ENDPOINT_URL_PRODUCTION = 'https://kpaymentgateway-services.kasikornbank.com';
    protected const ENDPOINT_JS_URL_SANDBOX = 'https://dev-kpaymentgateway.kasikornbank.com/ui/v2/kpayment.min.js';
    protected const ENDPOINT_JS_URL_PRODUCTION = 'https://kpaymentgateway.kasikornbank.com/ui/v2/kpayment.min.js';
    protected const KEY_EXPIRED_PENDING_TIME = 'expired_pending_time';
    protected const KEY_3DS_SUPPORT = 'is_3ds_support';
    public const KEY_TERMINAL_ID = 'terminal_id';
    public const KEY_INSTALLMENT_SUPPORT = 'is_installment_support';
    public const KEY_SMART_PAY_ID = 'smartpay_id';
    public const KEY_PRE_DEFINED_INSTALLMENT_SELECTION = 'predefine_installment';
    public const KEY_INSTALLMENT_INFO = 'installment_info';
    public const KEY_MERCHANT_ID = 'merchant_id';
    protected const KEY_AUTO_INVOICE = 'auto_invoice';
    protected const KEY_FRONTEND_RESPONSE = 'auto_invoice';

    public const TRANSACTION_STATE_PRE_AUTH = 'Pre-Authorized';
    public const TRANSACTION_STATE_AUTH = 'Authorized';
    public const TRANSACTION_STATE_SETTLED = 'Settled';
    public const VALIDATION_3DS_VALIDATION_OBJECT = 'charge';
    public const VALID_STATUS = 'success';
    public const INVALID_STATUS = 'error';
    public const CALLBACK_VALID_STATUS = 'true';
    public const CALLBACK_INVALID_STATUS = 'false';

    protected const GENERAL_PATH_PATTERN = 'ghoster_kbank/general/%s';
    protected const PAYMENT_PATH_PATTERN = 'payment/%s/%s';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Get is activate
     *
     * @param ?int $storeId
     * @return bool
     */
    public function getIsActive(?int $storeId = null): bool
    {
        return (bool)$this->getValue(Config::KEY_ACTIVATE, $storeId);
    }

    /**
     * Gets the login id
     *
     * @param int|null $storeId
     * @return string
     */
    public function getPublicKey(?int $storeId = null): string
    {
        $publicKey = (string)$this->getGeneralConfig(Config::KEY_PUBLIC_KEY, $storeId);

        if (mb_detect_encoding($publicKey) !== 'ASCII') {
            return '';
        }

        return $publicKey;
    }

    /**
     * Gets the secret key id
     *
     * @param ?int $storeId
     * @return string
     */
    public function getSecretKey(?int $storeId = null): string
    {
        $secretKey = (string)$this->getGeneralConfig(Config::KEY_SECRET_KEY, $storeId);

        if (mb_detect_encoding($secretKey) !== 'ASCII') {
            return '';
        }

        return $secretKey;
    }

    /**
     * Get Shop name
     *
     * @param ?int $storeId
     * @return string
     */
    public function getShopName(?int $storeId = null): string
    {
        return (string)$this->getGeneralConfig(Config::KEY_SHOP_NAME, $storeId);
    }

    /**
     * Gets the current environment
     *
     * @param ?int $storeId
     * @return string
     */
    public function getEnvironment(?int $storeId = null): string
    {
        $config = $this->getGeneralConfig(Config::KEY_ENVIRONMENT, $storeId);
        return $config ?? Environment::ENVIRONMENT_SANDBOX;
    }

    /**
     * Is Debug mode
     *
     * @param ?int $storeId
     * @return bool
     */
    public function isDebugMode(?int $storeId = null): bool
    {
        return (bool)$this->getGeneralConfig(Config::KEY_DEBUG, $storeId);
    }

    /**
     * Gets the API endpoint URL
     *
     * @param ?int $storeId
     * @return string
     */
    public function getApiUrl(?int $storeId = null): string
    {
        $environment = $this->getEnvironment($storeId);

        return $environment === Environment::ENVIRONMENT_SANDBOX
            ? self::ENDPOINT_URL_SANDBOX
            : self::ENDPOINT_URL_PRODUCTION;
    }

    /**
     * Gets the API js embedded endpoint URL
     *
     * @param ?int $storeId
     * @return string
     */
    public function getJsSrc(?int $storeId = null): string
    {
        $environment = (string)$this->getEnvironment($storeId);

        return $environment === Environment::ENVIRONMENT_SANDBOX
            ? self::ENDPOINT_JS_URL_SANDBOX
            : self::ENDPOINT_JS_URL_PRODUCTION;
    }

    /**
     * Expired time
     *
     * @param ?int $storeId
     * @return ?int
     */
    public function getExpiredPendingTimes(?int $storeId = null): ?int
    {
        return $this->getGeneralConfig(Config::KEY_EXPIRED_PENDING_TIME, $storeId);
    }

    /**
     * Pre-defined Installment Selection
     *
     * @param ?int $storeId
     * @return bool
     */
    public function isPredefinedInstallmentSelection(?int $storeId = null): bool
    {
        return (bool)$this->getValue(Config::KEY_PRE_DEFINED_INSTALLMENT_SELECTION, $storeId);
    }

    /**
     * Merchant ID
     *
     * @param ?int $storeId
     * @return string|null
     */
    public function getMerchantId(?int $storeId = null): ?string
    {
        return (string)$this->getValue(Config::KEY_MERCHANT_ID, $storeId);
    }

    /**
     * Is 3ds
     *
     * @param ?int $storeId
     * @return bool
     */
    public function is3DS(?int $storeId = null): bool
    {
        return (bool)$this->getGeneralConfig(Config::KEY_3DS_SUPPORT, $storeId);
    }

    /**
     * Is auto invoice
     *
     * @param ?int $storeId
     * @return bool
     */
    public function isAutoInvoice(?int $storeId = null): bool
    {
        return (bool)$this->getGeneralConfig(Config::KEY_AUTO_INVOICE, $storeId);
    }

    /**
     * Get custom frontend response
     *
     * @param int|null $storeId
     * @return string
     */
    public function getFrontendResponseUrl(?int $storeId = null): string
    {
        if ($this->is3DS($storeId)) {
            return '';
        }
        return (string)$this->getGeneralConfig(Config::KEY_FRONTEND_RESPONSE, $storeId);
    }

    /**
     * Installment Support enabling
     *
     * @param ?int $storeId
     * @return bool
     */
    public function isInstallmentSupport(?int $storeId = null): bool
    {
        return (bool)$this->getValue(Config::KEY_INSTALLMENT_SUPPORT, $storeId);
    }

    /**
     * Get Terminal Info
     *
     * @param int|null $storeId
     * @return string|null
     */
    public function getTerminalId(?int $storeId = null): ?string
    {
        return (string)$this->getValue(Config::KEY_TERMINAL_ID, $storeId);
    }

    /**
     * Installment Information
     *
     * @param ?int $storeId
     * @return string|null
     */
    public function getInstallmentInfo(?int $storeId = null): ?string
    {
        return $this->getValue(Config::KEY_INSTALLMENT_INFO, $storeId);
    }

    /**
     * Get scope config
     *
     * @return ScopeConfigInterface
     * @deprecated 100.0.10
     * @see \Magento\Framework\App\ObjectManager\ConfigWriterInterface
     */
    protected function getScopeConfig(): ScopeConfigInterface
    {
        if ($this->scopeConfig === null) {
            $this->scopeConfig = ObjectManager::getInstance()->get(
                ScopeConfigInterface::class
            );
        }

        return $this->scopeConfig;
    }

    /**
     * General configuration
     *
     * @param mixed $field
     * @param ?int $storeId
     * @return mixed
     */
    public function getGeneralConfig($field, ?int $storeId = null)
    {
        return $this->getScopeConfig()->getValue(
            sprintf(self::GENERAL_PATH_PATTERN, $field),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Specific payment configuration value
     *
     * @param string $paymentCode
     * @param string $field
     * @param int|null $storeId
     * @return mixed
     */
    public function getSpecificPaymentConfig(string $paymentCode, string $field, ?int $storeId = null)
    {
        return $this->getScopeConfig()->getValue(
            sprintf(self::PAYMENT_PATH_PATTERN, $paymentCode, $field),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}

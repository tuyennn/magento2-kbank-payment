<?php

namespace GhoSter\KbankPayments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\Data\CartInterface;
use GhoSter\KbankPayments\Gateway\Config;
use Magento\Framework\Serialize\SerializerInterface;
use GhoSter\KbankPayments\Api\InstallmentManagementInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CartInterface
     */
    private $cart;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var InstallmentManagementInterface
     */
    protected $installmentManagement;

    /**
     * @var MethodInterface[]
     */
    protected $methods = [];

    /**
     * @param Config $config
     * @param CartInterface $cart
     * @param UrlInterface $urlBuilder
     * @param PaymentHelper $paymentHelper
     * @param SerializerInterface $serializer
     * @param InstallmentManagementInterface $installmentManagement
     * @param array $methodCodes
     * @throws LocalizedException
     */
    public function __construct(
        Config $config,
        CartInterface $cart,
        UrlInterface $urlBuilder,
        PaymentHelper $paymentHelper,
        SerializerInterface $serializer,
        InstallmentManagementInterface $installmentManagement,
        array $methodCodes = []
    ) {
        $this->config = $config;
        $this->cart = $cart;
        $this->urlBuilder = $urlBuilder;
        $this->serializer = $serializer;
        $this->installmentManagement = $installmentManagement;

        foreach ($methodCodes as $code) {
            $this->methods[$code] = $paymentHelper->getMethodInstance($code);
        }
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig(): array
    {
        $configValues = [];
        $storeId = $this->cart->getStoreId();

        foreach ($this->methods as $methodCode => $method) {
            if ($method->isAvailable()) {
                $configValues = array_merge_recursive($configValues, [
                    'payment' => [
                        $methodCode => [
                            'processTokenActionUrl' => $this->getProcessTokenActionUrl(),
                            'publicKey' => $this->config->getPublicKey($storeId),
                            'jsSrc' => $this->config->getJsSrc($storeId),
                            'environment' => $this->config->getEnvironment($storeId),
                            'merchantId' => $method->getConfigData(Config::KEY_MERCHANT_ID),
                            'terminalId' => $method->getConfigData(Config::KEY_TERMINAL_ID),
                            'shopName' => $this->config->getShopName($storeId),
                            'failureUrl' => $this->getFailureUrl(),
                            'isUseInstallment' => (bool)$method->getConfigData(Config::KEY_INSTALLMENT_SUPPORT),
                            'isPreDefinedInstallment' => (bool)$method->getConfigData(
                                Config::KEY_PRE_DEFINED_INSTALLMENT_SELECTION
                            ),
                            'allowedInstallment' =>
                                $this->installmentManagement->getAvailableInstallment($methodCode)
                        ],
                    ]
                ]);
            }
        }

        return $configValues;
    }

    /**
     * Get popup action URL
     *
     * @return string
     */
    protected function getProcessTokenActionUrl(): string
    {
        return $this->urlBuilder->getUrl('kbank/token/process', ['_secure' => true]);
    }

    /**
     * Get failure url
     *
     * @return string
     */
    public function getFailureUrl(): string
    {
        return $this->urlBuilder->getUrl('checkout/onepage/failure', ['_secure' => true]);
    }
}

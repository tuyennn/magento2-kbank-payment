<?php

namespace GhoSter\KbankPayments\Model;

use GhoSter\KbankPayments\Gateway\Config;
use Magento\Framework\Serialize\SerializerInterface;

class InstallmentManagement implements \GhoSter\KbankPayments\Api\InstallmentManagementInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param Config $config
     * @param SerializerInterface $serializer
     */
    public function __construct(Config $config, SerializerInterface $serializer)
    {
        $this->config = $config;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function getAvailableInstallment(string $methodCode, ?int $storeId = null): array
    {
        $availableInstallmentsStripped = [];
        $availableInstallments = $this->config->getSpecificPaymentConfig($methodCode, Config::KEY_INSTALLMENT_INFO);

        if (!$availableInstallments) {
            return $availableInstallmentsStripped;
        }

        try {
            $configurations = $this->serializer->unserialize($availableInstallments);
            $i = 0;
            if (is_array($configurations)) {
                foreach ($configurations as $configuration) {
                    if (!$configuration['status']) {
                        continue;
                    }
                    $availableInstallmentsStripped[$i]['smartpay_id'] = preg_replace(
                        '/\s+/',
                        '',
                        $configuration['smartpay_id']
                    );
                    $availableInstallmentsStripped[$i]['payment_term'] = preg_replace(
                        '/\s+/',
                        '',
                        $configuration['payment_term']
                    );
                    $availableInstallmentsStripped[$i]['installment_title'] = trim($configuration['installment_title']);
                    $i++;
                }
            }
        } catch (\Exception $e) { //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
        } finally {
            return $availableInstallmentsStripped;
        }
    }
}

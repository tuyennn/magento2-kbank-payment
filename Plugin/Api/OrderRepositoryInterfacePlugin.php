<?php

namespace GhoSter\KbankPayments\Plugin\Api;

use GhoSter\KbankPayments\Gateway\Config;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use GhoSter\KbankPayments\Api\MetaRepositoryInterface;

class OrderRepositoryInterfacePlugin
{
    /**
     * @var MetaRepositoryInterface
     */
    private $metaRepository;

    /**
     * @var OrderExtensionFactory
     */
    protected $orderExtensionFactory;

    /**
     * @param MetaRepositoryInterface $metaRepository
     * @param OrderExtensionFactory $orderExtensionFactory
     */
    public function __construct(
        MetaRepositoryInterface $metaRepository,
        OrderExtensionFactory $orderExtensionFactory
    ) {
        $this->metaRepository = $metaRepository;
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    /**
     * Add more fields
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order): OrderInterface
    {
        $this->addSalesExtensionData($order);
        return $order;
    }

    /**
     * Add more fields
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $orderSearchResult
     * @return OrderSearchResultInterface
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $orderSearchResult
    ): OrderSearchResultInterface {
        foreach ($orderSearchResult->getItems() as $item) {
            $this->addSalesExtensionData($item);
        }

        return $orderSearchResult;
    }

    /**
     * Append sales order extension data
     *
     * @param OrderInterface $order
     * @return void
     */
    protected function addSalesExtensionData(OrderInterface $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();
        $orderExtension = $extensionAttributes ?: $this->orderExtensionFactory->create();

        $payment = $order->getPayment();

        if (!in_array(
            $payment->getMethod(),
            [
                Config::METHOD_KBANK_EMBEDDED_INSTALLMENT,
                Config::METHOD_KBANK_EMBEDDED_FULLPAYMENT,
                Config::METHOD_KBANK_INLINE
            ]
        )) {
            return;
        }

        try {
            $meta = $this->metaRepository->getByOrderIncrement($order->getIncrementId());
            if ($meta->getMetaId()) {

                $orderExtension->setCreditCardNo($meta->getMaskedPan());
                $orderExtension->setCreditType($meta->getCardBrand());

                $order->setExtensionAttributes($orderExtension);
            }
        } catch (\Exception $e) {
            return;
        }
    }
}

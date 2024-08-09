<?php

namespace GhoSter\KbankPayments\Observer;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use GhoSter\KbankPayments\Api\Data\MetaInterface;
use GhoSter\KbankPayments\Gateway\Config as KbankEmbeddedConfig;
use GhoSter\KbankPayments\Model\TokenFactory;
use GhoSter\KbankPayments\Model\TransactionProcessor;
use Psr\Log\LoggerInterface as Logger;

/**
 * Callback Validation event
 */
class ThreeDSValidationObserver extends AbstractDataAssignObserver
{
    /**
     * @var TransactionProcessor
     */
    protected $transactionProcessor;

    /**
     * @var TokenFactory
     */
    protected $tokenFactory;

    /**
     * @var KbankEmbeddedConfig
     */
    private $kbankEmbeddedConfig;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * DataAssignObserver constructor.
     * @param TransactionProcessor $transactionProcessor
     * @param TokenFactory $tokenFactory
     * @param KbankEmbeddedConfig $kbankEmbeddedConfig
     * @param OrderRepositoryInterface $orderRepository
     * @param Logger $logger
     */
    public function __construct(
        TransactionProcessor $transactionProcessor,
        TokenFactory $tokenFactory,
        KbankEmbeddedConfig $kbankEmbeddedConfig,
        OrderRepositoryInterface $orderRepository,
        Logger $logger
    ) {
        $this->transactionProcessor = $transactionProcessor;
        $this->tokenFactory = $tokenFactory;
        $this->kbankEmbeddedConfig = $kbankEmbeddedConfig;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        /** @var MetaInterface $meta */
        $meta = $observer->getData('meta');

        /** @var Order $order */
        $order = $observer->getData('order');

        /** @var DataObject */
        $errorObj = $observer->getData('error');
        $error = $errorObj->getError() ?? [];
        $type = (string)$observer->getData('type');

        try {
            $resultResponse = $this->transactionProcessor
                ->transactionDetail($order, $meta->getChargeId());

            if (!isset($resultResponse['object'])
                || !isset($resultResponse['transaction_state'])
            ) {
                $error[] = __('Invalid returned while inquiring or invalid transaction state');
                return $this;
            }

            if ($resultResponse['object'] == KbankEmbeddedConfig::VALIDATION_3DS_VALIDATION_OBJECT
                && $resultResponse['transaction_state'] == KbankEmbeddedConfig::TRANSACTION_STATE_AUTH
            ) {
                $order->setState(Order::STATE_PROCESSING)
                    ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING));
                $order->addStatusToHistory($order->getStatus(), __('3DS Payment Authorized.'));

                $this->logger->info(sprintf('3DS Payment Authorized by %s', $type));

                if ($this->kbankEmbeddedConfig->isAutoInvoice()
                    && $order->canInvoice()
                ) {
                    $this->transactionProcessor->createInvoice($order);
                }

                $this->orderRepository->save($order);
            }
        } catch (\Exception $e) {
            $error[] = $e->getMessage();
        } finally {
            $errorObj->setData('error', $error);
        }

        return $this;
    }
}

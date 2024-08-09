<?php

namespace GhoSter\KbankPayments\Cron;

use GhoSter\KbankPayments\Api\MetaRepositoryInterface;
use GhoSter\KbankPayments\Gateway\Config as KbankEmbeddedConfig;
use Psr\Log\LoggerInterface as Logger;
use GhoSter\KbankPayments\Model\TransactionProcessor;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class CancelExpiredPendingOrder
{
    /**
     * @var TransactionProcessor
     */
    protected $transactionProcessor;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var MetaRepositoryInterface
     */
    protected $metaRepository;

    /**
     * @var Iterator
     */
    private $iterator;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

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
     * CancelExpiredPendingOrder constructor.
     * @param TransactionProcessor $transactionProcessor
     * @param OrderManagementInterface $orderManagement
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param OrderFactory $orderFactory
     * @param MetaRepositoryInterface $metaRepository
     * @param Iterator $iterator
     * @param KbankEmbeddedConfig $kbankEmbeddedConfig
     * @param OrderRepositoryInterface $orderRepository
     * @param Logger $logger
     */
    public function __construct(
        TransactionProcessor $transactionProcessor,
        OrderManagementInterface $orderManagement,
        OrderCollectionFactory $orderCollectionFactory,
        OrderFactory $orderFactory,
        MetaRepositoryInterface $metaRepository,
        Iterator $iterator,
        KbankEmbeddedConfig $kbankEmbeddedConfig,
        OrderRepositoryInterface $orderRepository,
        Logger $logger
    ) {
        $this->transactionProcessor = $transactionProcessor;
        $this->orderManagement = $orderManagement;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderFactory = $orderFactory;
        $this->metaRepository = $metaRepository;
        $this->iterator = $iterator;
        $this->kbankEmbeddedConfig = $kbankEmbeddedConfig;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * Cancel the deprecated order
     *
     * @return $this
     */
    public function execute()
    {
        $expiredPendingMinutes = $this->kbankEmbeddedConfig->getExpiredPendingTimes();

        if (!$expiredPendingMinutes) {
            return $this;
        }

        /** @var $orders OrderCollection */
        $orders = $this->orderCollectionFactory->create();
        $orders->addFieldToFilter('status', ['in' => ['pending', 'pending_payment']]);
        $orders->getSelect()->where(
            new \Zend_Db_Expr(
                'TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP, `updated_at`)) >= ' . $expiredPendingMinutes * 60
            )
        );

        $orders->getSelect()
            ->join(
                ["sop" => "sales_order_payment"],
                'main_table.entity_id = sop.parent_id',
                ['method']
            )
            ->where(
                'sop.method IN (?)',
                [
                    KbankEmbeddedConfig::METHOD_KBANK_INLINE,
                    KbankEmbeddedConfig::METHOD_KBANK_EMBEDDED_INSTALLMENT,
                    KbankEmbeddedConfig::METHOD_KBANK_EMBEDDED_FULLPAYMENT
                ]
            )
            ->limit(5);

        $orders->setOrder(
            'created_at',
            'desc'
        );

        if ($orders->getSize() > 0) {
            $this->iterator->walk(
                $orders->getSelect(),
                [[$this, 'callbackUpdateKbankOrders']],
                [
                    'order' => $this->orderFactory->create()
                ]
            );
        }

        return $this;
    }

    /**
     * Callback update
     *
     * @param array $args
     * @throws \Exception
     */
    public function callbackUpdateKbankOrders($args)
    {
        /** @var Order $order */
        $order = $args['order'];
        $order->setData($args['row']);

        try {
            $meta = $this->metaRepository->getByOrderIncrement($order->getIncrementId());
            $resultResponse = $this->transactionProcessor
                ->transactionDetail($order, $meta->getChargeId());

            $this->processInquiryResponse($order, $resultResponse);
        } catch (\Exception $e) {
            $this->logger->error('Error while processing deprecated orders: ' . $e->getMessage());
            $order->cancel();
            $this->orderRepository->save($order);
        } finally {
            return;
        }
    }

    /**
     * Try to Inquiry order before cancel
     *
     * @param Order $order
     * @param array $resultResponse
     */
    protected function processInquiryResponse(Order $order, array $resultResponse)
    {
        if (!isset($resultResponse['object'])
            || !isset($resultResponse['transaction_state'])
        ) {
            $order->addCommentToStatusHistory(
                __("Invalid returned while inquiring or invalid transaction state"),
                $order->getStatus(),
            )->setIsCustomerNotified(false);

            $order->cancel();
        }

        if ($resultResponse['object'] == KbankEmbeddedConfig::VALIDATION_3DS_VALIDATION_OBJECT
            && $resultResponse['transaction_state'] == KbankEmbeddedConfig::TRANSACTION_STATE_AUTH
        ) {
            $order->setState(Order::STATE_PROCESSING)
                ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING));
            $order->addStatusToHistory($order->getStatus(), __('Payment re-Authorized automatically.'));

            if ($this->kbankEmbeddedConfig->isAutoInvoice()
                && $order->canInvoice()
            ) {
                $this->transactionProcessor->createInvoice($order);
            }
        } else {
            $order->addCommentToStatusHistory(
                __("Invalid returned while inquiring or invalid transaction state"),
                $order->getStatus(),
            )->setIsCustomerNotified(false);
            $order->cancel();
        }

        $this->orderRepository->save($order);
    }
}

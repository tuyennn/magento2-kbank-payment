<?php

namespace GhoSter\KbankPayments\Model;

use GhoSter\KbankPayments\Api\Data\MetaInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Service\InvoiceService;
use GhoSter\KbankPayments\Api\Data\TokenInterface;
use GhoSter\KbankPayments\Api\TransactionProcessorInterface;
use GhoSter\KbankPayments\Gateway\Config as KbankPaymentsConfig;
use GhoSter\KbankPayments\Gateway\Http\Client as KbankPaymentsClient;
use GhoSter\KbankPayments\Gateway\Validator\TransactionResponseValidator;
use Psr\Log\LoggerInterface as Logger;
use GhoSter\KbankPayments\Api\TokenRepositoryInterface;

class TransactionProcessor implements TransactionProcessorInterface
{
    /**
     * @var DataObjectFactory
     */
    protected $objectFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var KbankPaymentsClient
     */
    protected $kbankClient;

    /**
     * @var KbankPaymentsConfig
     */
    protected $kbankConfig;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var MetaFactory
     */
    protected $metaFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var TransactionResponseValidator
     */
    private $transactionResponseValidator;

    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var TokenRepositoryInterface
     */
    protected $tokenRepository;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @param DataObjectFactory $objectFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param KbankPaymentsClient $kbankClient
     * @param KbankPaymentsConfig $kbankConfig
     * @param OrderRepositoryInterface $orderRepository
     * @param TransactionResponseValidator $transactionResponseValidator
     * @param \GhoSter\KbankPayments\Model\MetaFactory $metaFactory
     * @param InvoiceSender $invoiceSender
     * @param InvoiceService $invoiceService
     * @param Transaction $transaction
     * @param Logger $logger
     * @param TokenRepositoryInterface $tokenRepository
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        DataObjectFactory $objectFactory,
        DataObjectHelper $dataObjectHelper,
        KbankPaymentsClient $kbankClient,
        KbankPaymentsConfig $kbankConfig,
        OrderRepositoryInterface $orderRepository,
        TransactionResponseValidator $transactionResponseValidator,
        MetaFactory $metaFactory,
        InvoiceSender $invoiceSender,
        InvoiceService $invoiceService,
        Transaction $transaction,
        Logger $logger,
        TokenRepositoryInterface $tokenRepository,
        OrderFactory $orderFactory
    ) {
        $this->objectFactory = $objectFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->kbankClient = $kbankClient;
        $this->kbankConfig = $kbankConfig;
        $this->orderRepository = $orderRepository;
        $this->transactionResponseValidator = $transactionResponseValidator;
        $this->metaFactory = $metaFactory;
        $this->invoiceSender = $invoiceSender;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->logger = $logger;
        $this->tokenRepository = $tokenRepository;
        $this->orderFactory = $orderFactory;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function authorizeOrderByToken(
        Order $order,
        TokenInterface $token
    ): MetaInterface {
        /** @var Meta $meta */
        $meta = $this->metaFactory->create();

        $request = [
            'method_type' => 'POST',
            'payload_type' => 'createAuthorizeRequest'
        ];
        $data = [
            'amount' => $this->_formatAmount($order->getGrandTotal()),
            'currency' => $order->getOrderCurrencyCode(),
            'description' => $order->getIncrementId(),
            'source_type' => 'card',
            'mode' => 'token',
            'reference_order' => $order->getIncrementId(),
            'token' => $token->getToken()
        ];

        $terminalId = $token->getTerminalId();
        if (!$terminalId) {
            $terminalId = $order->getPayment()->getAdditionalInformation('terminal_id');
        }

        $smartpayId = $token->getSmartpayId();
        if (!$smartpayId) {
            $smartpayId = $order->getPayment()->getAdditionalInformation('smartpay_id');
        }

        $term = $token->getTerm();
        if (!$term) {
            $term = $order->getPayment()->getAdditionalInformation('term');
        }

        if (!empty($smartpayId)
            && !empty($terminalId)
        ) {
            $data = array_merge($data, [
                'additional_data' => [
                    'mid' => $token->getMid(),
                    'tid' => $terminalId,
                    'smartpay_id' => $smartpayId,
                    'term' => $term
                ]
            ]);
        }

        $dataPost = array_merge($request, $data);
        $resultResponse = $this->kbankClient->postRequest($dataPost);

        $result = $this->transactionResponseValidator->validate(
            array_merge($dataPost, ['response' => $resultResponse])
        );

        if ($result->isValid()) {
            $meta->setOrderId($order->getIncrementId())
                ->setCustomerId($order->getCustomerId())
                ->setChargeId($resultResponse['id'])
                ->setObject($resultResponse['object'])
                ->setAmount($resultResponse['amount'])
                ->setCurrency($resultResponse['currency'])
                ->setTransactionState($resultResponse['transaction_state'])
                ->setCreated($resultResponse['created'])
                ->setStatus($resultResponse['status'])
                ->setApprovalCode($resultResponse['approval_code'])
                ->setLivemode($resultResponse['livemode'])
                ->setFailureCode((string)$resultResponse['failure_code'])
                ->setFailureMessage((string)$resultResponse['failure_message'])
                ->setRedirectUrl($resultResponse['redirect_url'])
                ->setCardId($resultResponse['source']['id'])
                ->setCardBrand($resultResponse['source']['brand'])
                ->setIssuerBank($resultResponse['source']['issuer_bank'])
                ->setMaskedPan($resultResponse['source']['card_masking'])
                ->setRef1($resultResponse['ref_1'])
                ->setRef2($resultResponse['ref_2'])
                ->setRef3($resultResponse['ref_3']);

            $meta->save();

            if ($meta->getTransactionState() == KbankPaymentsConfig::TRANSACTION_STATE_AUTH) {
                $order->setState(Order::STATE_PROCESSING)
                    ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING));
                $order->addStatusToHistory($order->getStatus(), __('Non 3DS Payment Authorized.'));

                if ($this->kbankConfig->isAutoInvoice()
                    && $order->canInvoice()
                ) {
                    $this->createInvoice($order);
                }
            } elseif ($meta->getTransactionState() == KbankPaymentsConfig::TRANSACTION_STATE_PRE_AUTH) {
                $order->addStatusToHistory($order->getStatus(), __('3DS Payment Pre-Authorized.'));
            }
        } else {
            $message = 'Order is suspended as its authorizing amount of %1 is suspected to be fraudulent.';
            $order->addStatusToHistory(
                $order->getStatus(),
                __($message, $order->getIncrementId())
            );
            $order->registerCancellation(implode(', ', $result->getErrorCodes()));
        }

        $this->orderRepository->save($order);

        return $meta;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function authorizeOrder(Order $order): MetaInterface
    {
        /** @var Meta $meta */
        $meta = $this->metaFactory->create();

        try {
            $token = $this->tokenRepository->getByOrderIncrement($order->getIncrementId());
            return $this->authorizeOrderByToken($order, $token);
        } catch (\Exception $e) {
            return $meta;
        }
    }

    /**
     * @inheritDoc
     */
    public function authorizeOrderByTokenValue(string $tokenValue): MetaInterface
    {
        /** @var Meta $meta */
        $meta = $this->metaFactory->create();

        try {
            $token = $this->tokenRepository->getByToken($tokenValue);
            $orderIncrement = $token->getOrderId();
            $order = $this->orderFactory->create()->loadByIncrementId($orderIncrement);

            if ($order->getId()) {
                return $this->authorizeOrderByToken($order, $token);
            } else {
                return $meta;
            }

        } catch (\Exception $e) {
            return $meta;
        }
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function settleOrder(Order $order, $chargeId): array
    {
        $request = [
            'method_type' => 'POST',
            'payload_type' => 'createSettleRequest'
        ];
        $data = [
            'charge_id' => $chargeId,
            'amount' => $this->_formatAmount($order->getGrandTotal()),
            'type' => 'full'
        ];

        $dataPost = array_merge($request, $data);
        $resultResponse = $this->kbankClient->postRequest($dataPost);

        $result = $this->transactionResponseValidator->validate(
            array_merge($dataPost, ['response' => $resultResponse])
        );

        if ($result->isValid()) {
            $order->addStatusToHistory($order->getStatus(), __('Payment Captured.'));
            $this->orderRepository->save($order);
        }

        return $resultResponse;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function transactionDetail(Order $order, $chargeId): array
    {
        $request = [
            'method_type' => 'GET',
            'payload_type' => 'transactionDetailsRequest'
        ];
        $data = [
            'charge_id' => $chargeId,
        ];

        $dataPost = array_merge($request, $data);

        return $this->kbankClient->postRequest($dataPost);
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function voidOrder(Order $order, $chargeId): array
    {
        $request = [
            'method_type' => 'POST',
            'payload_type' => 'voidRequest'
        ];
        $data = [
            'charge_id' => $chargeId,
            'reason' => 'Cancel transaction for reference_order: ' . $order->getIncrementId()
        ];

        $dataPost = array_merge($request, $data);
        $resultResponse = $this->kbankClient->postRequest($dataPost);

        $result = $this->transactionResponseValidator->validate(
            array_merge($dataPost, ['response' => $resultResponse])
        );

        if ($result->isValid()) {
            $message = 'Order is voided as its authorizing amount of %1 on gateway.';
            $order->addStatusToHistory(
                $order->getStatus(),
                __($message, $order->getIncrementId())
            );
            $order->registerCancellation($message);
        } else {
            $message = 'Cannot void order %1 on gateway.';
            $order->addStatusToHistory(
                $order->getStatus(),
                __($message, $order->getOrderIncrementId())
            );
        }

        $this->orderRepository->save($order);

        return $resultResponse;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function refundOrder(Order $order, $chargeId): array
    {
        $request = [
            'method_type' => 'POST',
            'payload_type' => 'refundRequest'
        ];

        $data = [
            'charge_id' => $chargeId,
            'amount' => $this->_formatAmount($order->getGrandTotal()),
            'reason' => 'Refund transaction for reference_order: ' . $order->getIncrementId()
        ];

        $dataPost = array_merge($request, $data);
        $resultResponse = $this->kbankClient->postRequest($dataPost);

        $result = $this->transactionResponseValidator->validate(
            array_merge($dataPost, ['response' => $resultResponse])
        );

        if ($result->isValid()) {
            $message = 'Order is refunded as its authorizing amount of %1 on gateway.';
            $order->addStatusToHistory(
                $order->getStatus(),
                __($message, $order->getIncrementId())
            );
            $order->setState(Order::STATE_COMPLETE)
                ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_COMPLETE));
        } else {
            $message = 'Cannot refund order %1 on gateway.';
            $order->addStatusToHistory(
                $order->getStatus(),
                __($message, $order->getOrderIncrementId())
            );
        }

        $this->orderRepository->save($order);

        return $resultResponse;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function createInvoice(Order $order): int
    {
        try {
            $invoice = $this->invoiceService
                ->prepareInvoice($order);
            $invoice->register();
            $invoice->getOrder()->setIsInProcess(true);
            $invoice->save();

            $transactionSave = $this->transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );

            $transactionSave->save();

            $this->invoiceSender->send($invoice);

            $order->addStatusToHistory(
                $order->getStatus(),
                __('Notified customer about invoice creation #%1.', $invoice->getId()),
                true
            );

            $result = $order->getId();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $result = $order->getId();
        }

        return $result;
    }

    /**
     * Format Amount
     *
     * @param float $orderAmount
     * @return string
     */
    protected function _formatAmount($orderAmount): string
    {
        return number_format((float)$orderAmount, 2, '.', '');
    }
}

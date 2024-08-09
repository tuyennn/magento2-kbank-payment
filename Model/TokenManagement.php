<?php

namespace GhoSter\KbankPayments\Model;

use GhoSter\KbankPayments\Api\Data\MetaInterface;
use GhoSter\KbankPayments\Api\Data\TokenInterface;
use GhoSter\KbankPayments\Api\TokenManagementInterface;
use GhoSter\KbankPayments\Api\TokenRepositoryInterface;
use GhoSter\KbankPayments\Api\TransactionProcessorInterface;
use GhoSter\KbankPayments\Gateway\Config;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use GhoSter\KbankPayments\Model\MetaFactory;
use Magento\Sales\Model\Order;

class TokenManagement implements TokenManagementInterface
{
    /**
     * @var TokenRepositoryInterface
     */
    protected $tokenRepository;

    /**
     * @var TransactionProcessorInterface
     */
    protected $transactionProcessor;

    /**
     * @var OrderInterfaceFactory
     */
    protected $orderFactory;

    /**
     * @var MetaFactory
     */
    protected $metaFactory;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var ?Order
     */
    protected $order;

    /**
     * @param TokenRepositoryInterface $tokenRepository
     * @param TransactionProcessorInterface $transactionProcessor
     * @param OrderInterfaceFactory $orderFactory
     * @param MetaFactory $metaFactory
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(
        TokenRepositoryInterface $tokenRepository,
        TransactionProcessorInterface $transactionProcessor,
        OrderInterfaceFactory $orderFactory,
        MetaFactory $metaFactory,
        PaymentHelper $paymentHelper
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->transactionProcessor = $transactionProcessor;
        $this->orderFactory = $orderFactory;
        $this->metaFactory = $metaFactory;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @inheritDoc
     */
    public function saveTokenForOrder(TokenInterface $token, string $orderIncrement): TokenInterface
    {
        /** @var Order $order */
        $order = $this->orderFactory->create()->loadByIncrementId($orderIncrement);

        if (!$order->getId()) {
            return $token;
        }

        $this->order = $order;

        $token->setOrderId($orderIncrement);
        if (!$token->getTerminalId()) {
            $paymentCode = $order->getPayment()->getMethod();
            $paymentInstance = $this->paymentHelper->getMethodInstance($paymentCode);

            $token->setTerminalId($paymentInstance->getConfigData(
                Config::KEY_TERMINAL_ID,
                $order->getStoreId()
            ));
        }
        return $this->tokenRepository->save($token);
    }

    /**
     * @inheritDoc
     */
    public function saveTokenThenAuthorize(TokenInterface $token, string $orderIncrement): MetaInterface
    {
        /** @var Meta $meta */
        $meta = $this->metaFactory->create();

        $token = $this->saveTokenForOrder($token, $orderIncrement);

        if (!$token->getTokenId() || null === $this->order) {
            return $meta;
        }

        return $this->transactionProcessor->authorizeOrderByToken($this->order, $token);
    }
}

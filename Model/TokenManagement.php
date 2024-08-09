<?php

namespace GhoSter\KbankPayments\Model;

use GhoSter\KbankPayments\Api\Data\MetaInterface;
use GhoSter\KbankPayments\Api\Data\TokenInterface;
use GhoSter\KbankPayments\Api\TokenManagementInterface;
use GhoSter\KbankPayments\Api\TokenRepositoryInterface;
use GhoSter\KbankPayments\Api\TransactionProcessorInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use GhoSter\KbankPayments\Model\MetaFactory;

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
     * @param TokenRepositoryInterface $tokenRepository
     * @param TransactionProcessorInterface $transactionProcessor
     * @param OrderInterfaceFactory $orderFactory
     * @param MetaFactory $metaFactory
     */
    public function __construct(
        TokenRepositoryInterface $tokenRepository,
        TransactionProcessorInterface $transactionProcessor,
        OrderInterfaceFactory $orderFactory,
        MetaFactory $metaFactory
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->transactionProcessor = $transactionProcessor;
        $this->orderFactory = $orderFactory;
        $this->metaFactory = $metaFactory;
    }

    /**
     * @inheritDoc
     */
    public function saveTokenForOrder(TokenInterface $token, string $orderIncrement): TokenInterface
    {
        $token->setOrderId($orderIncrement);
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
        $order = $this->orderFactory->create()->loadByIncrementId($orderIncrement);

        if ($token->getTokenId() && $order->getEntityId()) {
            return $this->transactionProcessor->authorizeOrderByToken($order, $token);
        }

        return $meta;
    }
}

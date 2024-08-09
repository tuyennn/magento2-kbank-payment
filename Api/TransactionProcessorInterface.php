<?php

namespace GhoSter\KbankPayments\Api;

use Magento\Sales\Model\Order;
use GhoSter\KbankPayments\Api\Data\TokenInterface;
use GhoSter\KbankPayments\Api\Data\MetaInterface;

/**
 * @api
 */
interface TransactionProcessorInterface
{
    /**
     * Authorize the order by token value
     *
     * @param string $tokenValue
     * @return MetaInterface
     */
    public function authorizeOrderByTokenValue(string $tokenValue): MetaInterface;

    /**
     * Authorize the order by token
     *
     * @param Order $order
     * @param TokenInterface $token
     * @return MetaInterface
     */
    public function authorizeOrderByToken(Order $order, TokenInterface $token): MetaInterface;

    /**
     * Authorize the order
     *
     * @param Order $order
     * @return MetaInterface
     */
    public function authorizeOrder(Order $order): MetaInterface;

    /**
     * Settle the order
     *
     * @param Order $order
     * @param mixed $chargeId
     * @return array
     */
    public function settleOrder(Order $order, $chargeId): array;

    /**
     * Get Transaction Detail
     *
     * @param Order $order
     * @param mixed $chargeId
     * @return array
     */
    public function transactionDetail(Order $order, $chargeId): array;

    /**
     * Void the oder
     *
     * @param Order $order
     * @param mixed $chargeId
     * @return array
     */
    public function voidOrder(Order $order, $chargeId): array;

    /**
     * Refund the order
     *
     * @param Order $order
     * @param mixed $chargeId
     * @return array
     */
    public function refundOrder(Order $order, $chargeId): array;

    /**
     * Create Invoice for order
     *
     * @param Order $order
     * @return int
     */
    public function createInvoice(Order $order): int;
}

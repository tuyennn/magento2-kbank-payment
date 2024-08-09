<?php

namespace GhoSter\KbankPayments\Model;

use Magento\Sales\Model\Order;

/**
 * Class PaymentStatus provide status for orders
 */
class PaymentStatus
{
    public const PAYMENT_PENDING = 'payment_pending';
    public const PAYMENT_PRE_AUTHORIZED = 'payment_preauthorized';
    public const PAYMENT_AUTHORIZED = 'payment_authorized';
    public const PAYMENT_VERIFIED = 'payment_verified';
    public const PAYMENT_CANCELED = 'payment_canceled';
    public const PAYMENT_EXPIRED = 'payment_expired';

    /**
     * Get Order State by Payment Status
     *
     * @param string $status
     * @return string
     */
    public function getOrderStateByPaymentStatus($status): string
    {
        switch ($status) {
            case self::PAYMENT_AUTHORIZED:
            case self::PAYMENT_VERIFIED:
            case self::PAYMENT_CANCELED:
            case self::PAYMENT_EXPIRED:
                return Order::STATE_PROCESSING;
            case self::PAYMENT_PENDING:
            default:
                return Order::STATE_PENDING_PAYMENT;
        }
    }
}

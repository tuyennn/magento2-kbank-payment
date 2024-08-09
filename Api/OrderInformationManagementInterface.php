<?php

namespace GhoSter\KbankPayments\Api;

/**
 * Interface OrderInformationManagementInterface
 * @api
 */
interface OrderInformationManagementInterface
{
    /**
     * Validate Order Status
     *
     * @param int $orderId
     * @return bool
     */
    public function validateOrderStatus(int $orderId): bool;
}

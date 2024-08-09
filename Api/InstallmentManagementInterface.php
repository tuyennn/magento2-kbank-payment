<?php

namespace GhoSter\KbankPayments\Api;

/**
 * @api
 */
interface InstallmentManagementInterface
{
    /**
     * Configuration available installments
     *
     * @param string $methodCode
     * @param int|null $storeId
     * @return array
     */
    public function getAvailableInstallment(string $methodCode, ?int $storeId = null): array;
}

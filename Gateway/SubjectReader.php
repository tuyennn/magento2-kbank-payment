<?php
namespace GhoSter\KbankPayments\Gateway;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper;

/**
 * Helper for extracting information from the payment data structure
 */
class SubjectReader
{
    /**
     * Reads payment from subject
     *
     * @param array $subject
     * @return PaymentDataObjectInterface
     */
    public function readPayment(array $subject)
    {
        return Helper\SubjectReader::readPayment($subject);
    }

    /**
     * Reads store's ID, otherwise returns null.
     *
     * @param array $subject
     * @return int|null
     */
    public function readStoreId(array $subject)
    {
        $storeId = $subject['store_id'] ?? null;

        if (empty($storeId)) {
            try {
                $storeId = $this->readPayment($subject)
                    ->getOrder()
                    ->getStoreId();
                // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
            } catch (\InvalidArgumentException $e) {
                // No store id is current set
            }
        }

        return $storeId ? (int)$storeId : null;
    }

    /**
     * Reads amount from subject
     *
     * @param array $subject
     * @return string
     */
    public function readAmount(array $subject)
    {
        return (string)Helper\SubjectReader::readAmount($subject);
    }

    /**
     * Reads response from subject
     *
     * @param array $subject
     * @return array
     */
    public function readResponse(array $subject)
    {
        return Helper\SubjectReader::readResponse($subject);
    }

    /**
     * Reads login id from subject
     *
     * @param array $subject
     * @return string|null
     */
    public function readLoginId(array $subject)
    {
        return $subject['merchantAuthentication']['name'] ?? null;
    }

    /**
     * Reads transaction key from subject
     *
     * @param array $subject
     * @return string|null
     */
    public function readTransactionKey(array $subject)
    {
        return $subject['merchantAuthentication']['transactionKey'] ?? null;
    }
}

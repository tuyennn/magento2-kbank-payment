<?php

namespace GhoSter\KbankPayments\Gateway\Request;

use GhoSter\KbankPayments\Gateway\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Adds the meta transaction information to the request
 */
class RefundDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        SubjectReader $subjectReader
    ) {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();

        $data = [];

        if ($payment instanceof Payment) {
            $authTransaction = $payment->getAuthorizationTransaction();
            $chargeId = $authTransaction->getAdditionalInformation('charge_id');

            $data = [
                'charge_id' => $chargeId,
                'amount' => $order->getGrandTotalAmount(),
                'reason' => 'Refund transaction for reference_order: ' . $order->getOrderIncrementId()
            ];
        }

        return $data;
    }
}

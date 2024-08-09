<?php

namespace GhoSter\KbankPayments\Gateway\Request;

use GhoSter\KbankPayments\Api\MetaRepositoryInterface;
use GhoSter\KbankPayments\Gateway\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Adds the meta transaction information to the request
 */
class TransactionDetailsDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var MetaRepositoryInterface
     */
    private $metaRepository;

    /**
     * @param SubjectReader $subjectReader
     * @param MetaRepositoryInterface $metaRepository
     */
    public function __construct(
        SubjectReader $subjectReader,
        MetaRepositoryInterface $metaRepository
    ) {
        $this->subjectReader = $subjectReader;
        $this->metaRepository = $metaRepository;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $chargeId = null;
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();

        if ($payment instanceof Payment) {
            $authTransaction = $payment->getAuthorizationTransaction();
            $chargeId = $authTransaction->getAdditionalInformation('charge_id');
        }

        if (!$chargeId) {
            try {
                $order = $payment->getOrder();
                $meta = $this->metaRepository->getByOrderIncrement($order->getIncrementId());
                $chargeId = $meta->getChargeId();
            } catch (\Exception $e) {
                return [
                    'charge_id' => $chargeId
                ];
            }

        }

        return [
            'charge_id' => $chargeId
        ];
    }
}

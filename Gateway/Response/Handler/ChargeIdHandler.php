<?php

namespace GhoSter\KbankPayments\Gateway\Response\Handler;

use GhoSter\KbankPayments\Gateway\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Processes charge id for the payment
 *
 */
class ChargeIdHandler implements HandlerInterface
{
    public const CHARGE_OBJECT = 'charge';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $payment = $paymentDO->getPayment();

        if ($payment instanceof Payment && isset($response['id']) && isset($response['object'])) {
            if ($response['object'] === self::CHARGE_OBJECT) {
                $order = $payment->getOrder();

                $payment->setTransactionAdditionalInfo(
                    'charge_id',
                    $response['id']
                );

                $order->addStatusToHistory($order->getStatus(), 'Processed Charge Id: ' . $response['id']);
            }
        }
    }
}

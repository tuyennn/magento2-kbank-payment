<?php
namespace GhoSter\KbankPayments\Gateway\Request;

use GhoSter\KbankPayments\Gateway\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Adds the meta transaction information to the request
 */
class AuthorizeDataBuilder implements BuilderInterface
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

        $tokenValue = $payment->getAdditionalInformation('tokenValue');

        if ($payment instanceof Payment) {

            $data = [
                'amount' => $order->getGrandTotalAmount(),
                'currency' => $order->getCurrencyCode(),
                'description' => $order->getOrderIncrementId(),
                'source_type' => 'card',
                'mode' => 'token',
                'reference_order' => $order->getOrderIncrementId(),
                'token' => $tokenValue
            ];
        }

        return $data;
    }
}

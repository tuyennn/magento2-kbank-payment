<?php
namespace GhoSter\KbankPayments\Gateway\Response\Handler;

use GhoSter\KbankPayments\Gateway\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Processes payment information from a void transaction response
 */
class CloseTransactionHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var bool
     */
    private $closeTransaction;

    /**
     * @param SubjectReader $subjectReader
     * @param bool $closeTransaction
     */
    public function __construct(SubjectReader $subjectReader, bool $closeTransaction = true)
    {
        $this->subjectReader = $subjectReader;
        $this->closeTransaction = $closeTransaction;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $payment = $paymentDO->getPayment();

        if ($payment instanceof Payment) {
            $payment->setIsTransactionClosed($this->closeTransaction);
            $payment->setShouldCloseParentTransaction(true);
        }
    }
}

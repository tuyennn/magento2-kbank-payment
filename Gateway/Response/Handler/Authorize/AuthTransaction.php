<?php
namespace GhoSter\KbankPayments\Gateway\Response\Handler\Authorize;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Magento\Sales\Model\ResourceModel\Order\Payment as PaymentResource;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order\Payment;
use GhoSter\KbankPayments\Gateway\SubjectReader;

class AuthTransaction implements HandlerInterface
{
    /**
     * @var PaymentResource
     */
    private $paymentResource;
    /**
     * @var BuilderInterface
     */
    private $transactionBuilder;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AuthTransaction constructor.
     * @param SubjectReader $subjectReader
     * @param PaymentResource $paymentResource
     * @param BuilderInterface $transactionBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        SubjectReader $subjectReader,
        PaymentResource $paymentResource,
        BuilderInterface $transactionBuilder,
        LoggerInterface $logger
    ) {
        $this->subjectReader = $subjectReader;
        $this->paymentResource = $paymentResource;
        $this->transactionBuilder = $transactionBuilder;
        $this->logger = $logger;
    }

    /**
     * Handle the Auth
     *
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        try {
            $paymentDO = $this->subjectReader->readPayment($handlingSubject);
            $payment = $paymentDO->getPayment();

            if ($payment instanceof Payment) {
                $chargeId = $response['id'];
                $source = $response['source'] ?? [];

                $order = $payment->getOrder();
                $formattedPrice = $order->getBaseCurrency()->formatTxt(
                    $order->getGrandTotal()
                );

                $transaction = $this->transactionBuilder->setPayment($payment)
                    ->setOrder($order)
                    ->setTransactionId($chargeId)
                    ->setAdditionalInformation(
                        $payment->getTransactionAdditionalInfo()
                    )
                    ->setFailSafe(true)
                    ->build(TransactionInterface::TYPE_AUTH);

                $payment->setCcLast4($source['card_masking'] ? substr($source['card_masking'], -4) : '');
                $payment->setCcAvsStatus('N/A');
                $payment->setIsTransactionClosed(false);
                $payment->addTransactionCommentsToOrder(
                    $transaction,
                    __('The Payment Authorization success. The authorized amount is %1.', $formattedPrice)
                );
                $payment->setParentTransactionId(null);
            }
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }
}

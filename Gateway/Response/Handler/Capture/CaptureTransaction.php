<?php
namespace GhoSter\KbankPayments\Gateway\Response\Handler\Capture;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Payment as PaymentResource;
use Psr\Log\LoggerInterface;

class CaptureTransaction implements HandlerInterface
{
    /**
     * @var PaymentResource
     */
    protected $paymentResource;

    /**
     * @var BuilderInterface
     */
    protected $transactionBuilder;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * CaptureTransaction constructor.
     * @param PaymentResource $paymentResource
     * @param BuilderInterface $transactionBuilder
     * @param OrderFactory $orderFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        PaymentResource $paymentResource,
        BuilderInterface $transactionBuilder,
        OrderFactory $orderFactory,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->orderFactory = $orderFactory;
        $this->transactionBuilder = $transactionBuilder;
        $this->paymentResource = $paymentResource;
    }

    /**
     * Handle
     *
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        try {
            $payment = $handlingSubject['payment']->getPayment();

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
                    ->build(TransactionInterface::TYPE_CAPTURE);

                $payment->setCcLast4($source['card_masking'] ? substr($source['card_masking'], -4) : '');
                $payment->setCcAvsStatus('N/A');
                $payment->setAmountAuthorized($order->getGrandTotal());
                $payment->setCcNumberEnc($source['card_masking'] ?? '');
                $payment->addTransactionCommentsToOrder(
                    $transaction,
                    __('The Payment Capture success. Captured amount is %1.', $formattedPrice)
                );
                $payment->setParentTransactionId(null);
            }
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
        }
    }
}

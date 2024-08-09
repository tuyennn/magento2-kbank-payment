<?php
namespace GhoSter\KbankPayments\Gateway\Response\Handler\Authorize;

use Magento\Sales\Model\Order\Payment;
use GhoSter\KbankPayments\Model\PaymentStatus;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use GhoSter\KbankPayments\Gateway\SubjectReader;

class OrderStatus implements HandlerInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var PaymentStatus
     */
    private $paymentStatus;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * OrderStatus constructor.
     * @param SubjectReader $subjectReader
     * @param OrderRepositoryInterface $orderRepository
     * @param PaymentStatus $paymentStatus
     */
    public function __construct(
        SubjectReader $subjectReader,
        OrderRepositoryInterface $orderRepository,
        PaymentStatus $paymentStatus
    ) {
        $this->subjectReader = $subjectReader;
        $this->orderRepository = $orderRepository;
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * Handle
     *
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $payment = $paymentDO->getPayment();

        if ($payment instanceof Payment) {
            $order = $payment->getOrder();

            $order->setState($this->paymentStatus->getOrderStateByPaymentStatus(PaymentStatus::PAYMENT_AUTHORIZED))
                ->setStatus(PaymentStatus::PAYMENT_AUTHORIZED);
            $order->addStatusToHistory($order->getStatus(), 'Payment Authorized.');
            $this->orderRepository->save($order);
        }
    }
}

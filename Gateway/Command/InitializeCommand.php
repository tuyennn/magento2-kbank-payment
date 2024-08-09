<?php

namespace GhoSter\KbankPayments\Gateway\Command;

use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;

class InitializeCommand implements CommandInterface
{
    /**
     * Convert to pending_payment
     *
     * @param array $commandSubject
     * @return void
     */
    public function execute(array $commandSubject)
    {
        $stateObject = SubjectReader::readStateObject($commandSubject);
        $paymentDO = SubjectReader::readPayment($commandSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        $order = $payment->getOrder();
        ContextHelper::assertOrderPayment($payment);
        if (!$payment instanceof Payment) {
            throw new \LogicException('Order Payment should be provided');
        }

        $payment->setAmountAuthorized($order->getTotalDue());
        $payment->setBaseAmountAuthorized($order->getBaseTotalDue());

        $payment->getOrder()->setCanSendNewEmailFlag(false);
        $stateObject->setData(OrderInterface::STATE, Order::STATE_PENDING_PAYMENT);
        $stateObject->setData(OrderInterface::STATUS, Order::STATE_PENDING_PAYMENT);
        $stateObject->setData('is_notified', false);
    }
}

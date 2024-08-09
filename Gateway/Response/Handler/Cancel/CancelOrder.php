<?php

namespace GhoSter\KbankPayments\Gateway\Response\Handler\Cancel;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class CancelOrder implements HandlerInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ManagerInterface
     */
    private $evenManager;

    /**
     * CancelTransaction constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param ManagerInterface $evenManager
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ManagerInterface $evenManager
    ) {
        $this->orderRepository = $orderRepository;
        $this->evenManager = $evenManager;
    }

    /**
     * Handle the request
     *
     * @param array $handlingSubject
     * @param array $response
     * @throws LocalizedException
     */
    public function handle(array $handlingSubject, array $response)
    {
        /** @var $order Order */
        $order = $handlingSubject['order'];
        $order->registerCancellation('Payment Failed. Cancel Order.');

        $this->evenManager->dispatch('order_cancel_after', ['order' => $order]);
        $this->orderRepository->save($order);
    }
}

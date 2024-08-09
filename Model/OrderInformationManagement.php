<?php

namespace GhoSter\KbankPayments\Model;

use GhoSter\KbankPayments\Api\OrderInformationManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class OrderInformationManagement implements OrderInformationManagementInterface
{
    /**
     * @var array of allowed order states on frontend
     */
    protected $allowedOrderStates = [
        Order::STATE_PROCESSING,
        Order::STATE_COMPLETE,
    ];

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * OrderInformationManagement constructor.
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritDoc
     */
    public function validateOrderStatus(int $orderId): bool
    {
        try {
            $order = $this->orderRepository->get($orderId);
            return in_array($order->getState(), $this->allowedOrderStates);
        } catch (\Exception $e) {
            return false;
        }
    }
}

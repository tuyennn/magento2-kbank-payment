<?php
namespace GhoSter\KbankPayments\Gateway\Response\Handler\Capture;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderFactory;
use Psr\Log\LoggerInterface;

class OrderStatus implements HandlerInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * OrderStatus constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderFactory $orderFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderFactory $orderFactory,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->orderFactory = $orderFactory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Handle
     *
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        $order = $handlingSubject['order'];
        $order->addStatusToHistory($order->getStatus(), 'Payment Captured.');
        $this->orderRepository->save($order);
    }
}

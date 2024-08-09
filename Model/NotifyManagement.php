<?php

namespace GhoSter\KbankPayments\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use GhoSter\KbankPayments\Api\Data\MpiInterface;
use GhoSter\KbankPayments\Api\Data\NotifyInterface;
use GhoSter\KbankPayments\Api\Data\SourceInterface;
use GhoSter\KbankPayments\Api\MetaRepositoryInterface;
use GhoSter\KbankPayments\Api\NotifyManagementInterface;
use GhoSter\KbankPayments\Gateway\Config;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use GhoSter\KbankPayments\Model\Data\NotifyFactory as NotifyDataFactory;
use GhoSter\KbankPayments\Model\Data\MpiFactory as MpiDataFactory;
use GhoSter\KbankPayments\Model\Data\SourceFactory as SourceDataFactory;

class NotifyManagement implements NotifyManagementInterface
{
    /**
     * @var RestRequest
     */
    protected $request;

    /**
     * @var MetaRepositoryInterface
     */
    protected $metaRepository;

    /**
     * @var Config
     */
    protected $kbankEmbeddedConfig;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var NotifyDataFactory
     */
    protected $notifyDataFactory;

    /**
     * @var MpiDataFactory
     */
    protected $mpiDataFactory;

    /**
     * @var SourceDataFactory
     */
    protected $sourceDataFactory;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array of allowed order states on frontend
     */
    protected $allowedOrderStates = [
        Order::STATE_PROCESSING,
        Order::STATE_COMPLETE,
    ];

    /**
     * NotifyManagement constructor.
     * @param RestRequest $request
     * @param MetaRepositoryInterface $metaRepository
     * @param Config $config
     * @param OrderFactory $orderFactory
     * @param DataObjectFactory $dataObjectFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ManagerInterface $eventManager
     * @param NotifyDataFactory $notifyDataFactory
     * @param SourceDataFactory $sourceDataFactory
     * @param MpiDataFactory $mpiDataFactory
     * @param Logger $logger
     */
    public function __construct(
        RestRequest $request,
        MetaRepositoryInterface $metaRepository,
        Config $config,
        OrderFactory $orderFactory,
        DataObjectFactory $dataObjectFactory,
        DataObjectHelper $dataObjectHelper,
        ManagerInterface $eventManager,
        NotifyDataFactory $notifyDataFactory,
        SourceDataFactory $sourceDataFactory,
        MpiDataFactory $mpiDataFactory,
        Logger $logger
    ) {
        $this->request = $request;
        $this->metaRepository = $metaRepository;
        $this->kbankEmbeddedConfig = $config;
        $this->orderFactory = $orderFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->eventManager = $eventManager;
        $this->notifyDataFactory = $notifyDataFactory;
        $this->sourceDataFactory = $sourceDataFactory;
        $this->mpiDataFactory = $mpiDataFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function notify(): bool
    {
        $notifyData = $this->request->getBodyParams();

        /** @var SourceInterface $mpi */
        $source = $this->sourceDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $source,
            $notifyData['source'] ?? [],
            SourceInterface::class
        );

        /** @var MpiInterface $mpi */
        $mpi = $this->mpiDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $mpi,
            $notifyData['mpi'] ?? [],
            MpiInterface::class
        );

        /** @var NotifyInterface $notify */
        $notify = $this->notifyDataFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $notify,
            $notifyData,
            NotifyInterface::class
        );

        $notify->setSource($source)
            ->setMpi($mpi);

        if ($notify->getStatus() !== Config::VALID_STATUS) {
            $this->logger->info(sprintf('Status was mismatched or empty. %s', $notify->getStatus()));
            return false;
        }

        if (!$notify->getId()) {
            $this->logger->info(sprintf('Charge Id was mismatched or empty. %s', $notify->getId()));
            return false;
        }

        if (!$notify->getChecksum()) {
            $this->logger->info(sprintf('Checksum was mismatched or empty. %s', $notify->getChecksum()));
            return false;
        }

        $concatenate = $notify->getId()
            . number_format($notify->getAmount(), 4, '.', '')
            . $notify->getCurrency()
            . $notify->getStatus()
            . $notify->getTransactionState();

        $salt = $this->kbankEmbeddedConfig->getSecretKey();
        $expectedCheckSum = hash('sha256', $concatenate . $salt);

        if ($notify->getChecksum() !== $expectedCheckSum) {
            $this->logger->info(sprintf('Checksum was mismatched or empty. %s', $notify->getChecksum()));
            return false;
        }

        try {
            $meta = $this->metaRepository->getByChargeId($notify->getId());

            if (!$meta->getMetaId()) {
                $this->logger->info('Meta was mismatched or empty.');
                return false;
            }

            /** @var Order $order */
            $order = $this->orderFactory->create()
                ->loadByIncrementId($meta->getOrderId());

            if (!$order->getId() || $order->getIncrementId() !== $notify->getReferenceOrder()) {
                $this->logger->info(sprintf('Order was mismatched or empty. %s', $notify->getReferenceOrder()));
                return false;
            }

            if ($this->_checkOrderState($order)) {
                $this->logger->info(sprintf('Order status already changed. %s', $order->getStatus()));
            } else {
                $errorObject = $this->dataObjectFactory
                    ->create(['data' => ['error' => $this->errors]]);

                $this->eventManager->dispatch(
                    'kbank_embedded_payment_method_callback_validation',
                    [
                        'meta' => $meta,
                        'order' => $order,
                        'error' => $errorObject,
                        'type' => 'notify'
                    ]
                );

                if (!empty($errorObject->getData('error')) && is_array($errorObject->getData('error'))) {
                    $comment = implode(PHP_EOL, $errorObject->getData('error'));
                    $this->logger->critical("Something went wrong while validation\n");
                    $this->logger->critical($comment);

                    $order->cancel();
                    $order->save();
                    return false;
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical("Something went wrong while notify\n" . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Check order state
     *
     * @param Order $order
     * @return bool
     */
    protected function _checkOrderState(Order $order): bool
    {
        if (!$order->getEntityId()) {
            return false;
        }

        return in_array($order->getState(), $this->allowedOrderStates);
    }
}

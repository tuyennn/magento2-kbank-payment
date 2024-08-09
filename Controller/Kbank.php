<?php

namespace GhoSter\KbankPayments\Controller;

use GhoSter\KbankPayments\Api\Data\MetaInterfaceFactory;
use GhoSter\KbankPayments\Api\MetaRepositoryInterface;
use GhoSter\KbankPayments\Gateway\Config as KbankConfig;
use GhoSter\KbankPayments\Helper\Checkout as KbankCheckoutHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\PaymentFailuresInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use GhoSter\KbankPayments\Model\Url;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\HTTP\Header;

/**
 * Kbank Checkout Controller
 */
abstract class Kbank extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var MetaRepositoryInterface
     */
    protected $metaRepository;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var KbankCheckoutHelper
     */
    protected $checkoutHelper;

    /**
     * @var PaymentFailuresInterface
     */
    protected $paymentFailures;

    /**
     * @var KbankConfig
     */
    protected $kbankConfig;

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var MetaInterfaceFactory
     */
    protected $metaDataFactory;

    /**
     * @var ?Url
     */
    protected $url;

    /**
     * @var ?Header
     */
    protected $httpHeader;

    /**
     * @var array of allowed order states for processing
     */
    protected $allowedOrderStates = [
        Order::STATE_NEW
    ];

    /**
     * @var array
     */
    protected $processedOrderStates = [
        Order::STATE_PROCESSING,
        Order::STATE_COMPLETE,
    ];

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param CheckoutSession $checkoutSession
     * @param OrderFactory $orderFactory
     * @param KbankCheckoutHelper $checkoutHelper
     * @param MetaRepositoryInterface $metaRepository
     * @param Logger $logger
     * @param DataObjectFactory $dataObjectFactory
     * @param KbankConfig|null $kbankConfig
     * @param PaymentFailuresInterface|null $paymentFailures
     * @param MetaInterfaceFactory|null $metaDataFactory
     * @param Url|null $url
     * @param Header|null $header
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        CheckoutSession $checkoutSession,
        OrderFactory $orderFactory,
        KbankCheckoutHelper $checkoutHelper,
        MetaRepositoryInterface $metaRepository,
        Logger $logger,
        DataObjectFactory $dataObjectFactory,
        KbankConfig $kbankConfig = null,
        PaymentFailuresInterface $paymentFailures = null,
        MetaInterfaceFactory $metaDataFactory = null,
        Url $url = null,
        Header $header = null
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
        $this->checkoutHelper = $checkoutHelper;
        $this->metaRepository = $metaRepository;
        $this->logger = $logger;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->kbankConfig = $kbankConfig ?: ObjectManager::getInstance()
            ->get(KbankConfig::class);
        $this->paymentFailures = $paymentFailures ?: ObjectManager::getInstance()
            ->get(PaymentFailuresInterface::class);

        $this->metaDataFactory = $metaDataFactory ?: ObjectManager::getInstance()
            ->get(MetaInterfaceFactory::class);

        $this->url = $url ?: ObjectManager::getInstance()
            ->get(Url::class);
        $this->httpHeader = $header ?: ObjectManager::getInstance()
            ->get(Header::class);
        parent::__construct($context);
    }
}

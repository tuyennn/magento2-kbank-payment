<?php

namespace GhoSter\KbankPayments\Observer;

use GhoSter\KbankPayments\Gateway\Config;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Sales\Model\Order;
use GhoSter\KbankPayments\Model\Token;
use GhoSter\KbankPayments\Model\TokenFactory;
use GhoSter\KbankPayments\Model\TransactionProcessor;
use GhoSter\KbankPayments\Model\TokenRepository;

/**
 * Adds the payment info to the payment object
 *
 */
class ProcessTokenDataObserver extends AbstractDataAssignObserver
{
    /**
     * @var array
     */
    private $additionalInformationMappingList = [
        'smartpayId' => 'smartpay_id',
        'smartpayid' => 'smartpay_id',
        'smartpay_id' => 'smartpay_id',
        'mid' => 'mid',
        'mId' => 'mid',
        'term' => 'term',
        'terminal_id' => 'terminal_id',
        'terminalId' => 'terminal_id',
        'token' => 'token',
        'paymentMethods' => 'payment_methods',
        'payment_methods' => 'payment_methods',
        'dcc_currency' => 'dcc_currency',
        'saveCard' => 'save_card',
        'save_card' => 'save_card',
        'cardid' => 'card_id',
        'card_id' => 'card_id'
    ];

    /**
     * @var TransactionProcessor
     */
    protected $transactionProcessor;

    /**
     * @var TokenFactory
     */
    protected $tokenFactory;

    /**
     * @var TokenRepository
     */
    protected $tokenRepository;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * DataAssignObserver constructor.
     *
     * @param TransactionProcessor $transactionProcessor
     * @param TokenFactory $tokenFactory
     * @param TokenRepository $tokenRepository
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(
        TransactionProcessor $transactionProcessor,
        TokenFactory $tokenFactory,
        TokenRepository $tokenRepository,
        PaymentHelper $paymentHelper
    ) {
        $this->transactionProcessor = $transactionProcessor;
        $this->tokenFactory = $tokenFactory;
        $this->tokenRepository = $tokenRepository;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        /** @var array $gatewayTokenData */
        $gatewayTokenData = $observer->getData('gateway_token');

        /** @var Order $order */
        $order = $observer->getData('order');

        /** @var DataObject $result */
        $result = $observer->getData('result');

        if (empty($gatewayTokenData)) {
            return $this;
        }

        /** @var Token $token */
        $token = $this->tokenFactory->create();
        $token->setOrderId($order->getIncrementId())
            ->setCustomerId($order->getCustomerId());
        foreach ($this->additionalInformationMappingList as $dataKey => $dataMap) {
            if (isset($gatewayTokenData[$dataKey])) {
                $token->setData($dataMap, $gatewayTokenData[$dataKey]);
            }
        }

        $paymentCode = $order->getPayment()->getMethod();
        $paymentInstance = $this->paymentHelper->getMethodInstance($paymentCode);

        if (!$token->getTerminalId()) {
            $token->setTerminalId(
                $paymentInstance->getConfigData(
                    Config::KEY_TERMINAL_ID,
                    $order->getStoreId()
                )
            );
        }

        try {
            $token = $this->tokenRepository->save($token);

            $meta = $this->transactionProcessor
                ->authorizeOrderByToken($order, $token);

            $observer->setData('meta', $meta);
        } catch (\Exception $e) {
            $result->setData('result', false)
                ->setData('message', __('Sorry, but we can\'t authorize your order. Please try again later'));
        }
        return $this;
    }
}

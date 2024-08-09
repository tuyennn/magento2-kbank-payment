<?php

namespace GhoSter\KbankPayments\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @api
 */
interface TokenInterface extends ExtensibleDataInterface
{
    public const TOKEN_ID = 'token_id';
    public const CUSTOMER_ID = 'customer_id';
    public const TOKEN = 'token';
    public const ORDER_ID = 'order_id';
    public const PAYMENT_METHODS = 'payment_methods';
    public const CREATED_TIME = 'created_time';
    public const DCC_CURRENCY = 'dcc_currency';
    public const SMARTPAY_ID = 'smartpay_id';
    public const TERM = 'term';
    public const SAVECARD = 'save_card';
    public const CARDID = 'card_id';
    public const MID = 'mid';
    public const TERMINAL_ID = 'terminal_id';

    /**
     * Get Token Id
     *
     * @return string
     */
    public function getTokenId();

    /**
     * Set Token Id
     *
     * @param string $tokenId
     * @return $this
     */
    public function setTokenId($tokenId);

    /**
     * Get Token
     *
     * @return string
     */
    public function getToken();

    /**
     * Set Token
     *
     * @param string $token
     * @return $this
     */
    public function setToken($token);

    /**
     * Get Order Id
     *
     * @return string
     */
    public function getOrderId();

    /**
     * Set Order Id
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Get Customer Id
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set Customer Id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get Payment Methods
     *
     * @return string
     */
    public function getPaymentMethods();

    /**
     * Set Payment Method
     *
     * @param string $paymentMethods
     * @return $this
     */
    public function setPaymentMethods($paymentMethods);

    /**
     * Get Created Time
     *
     * @return string
     */
    public function getCreatedTime();

    /**
     * Set Created Time
     *
     * @param string $createdTime
     * @return $this
     */
    public function setCreatedTime($createdTime);

    /**
     * Get Dcc Currency
     *
     * @return string
     */
    public function getDccCurrency();

    /**
     * Set Dcc Currency
     *
     * @param string $dccCurrency
     * @return $this
     */
    public function setDccCurrency($dccCurrency);

    /**
     * Get SmartPay Id
     *
     * @return string
     */
    public function getSmartpayId();

    /**
     * Set SmartPay Id
     *
     * @param string $smartpayId
     * @return $this
     */
    public function setSmartpayId($smartpayId);

    /**
     * Get Term
     *
     * @return string
     */
    public function getTerm();

    /**
     * Set Term
     *
     * @param string $term
     * @return $this
     */
    public function setTerm($term);

    /**
     * Get Saved Card
     *
     * @return string
     */
    public function getSaveCard();

    /**
     * Set Saved Card
     *
     * @param string $saveCard
     * @return $this
     */
    public function setSaveCard($saveCard);

    /**
     * Get card Id
     *
     * @return string
     */
    public function getCardId();

    /**
     * Set Card Id
     *
     * @param string $cardId
     * @return $this
     */
    public function setCardId($cardId);

    /**
     * Get Mid
     *
     * @return string
     */
    public function getMid();

    /**
     * Set Mid
     *
     * @param string $mid
     * @return $this
     */
    public function setMid($mid);

    /**
     * Get Terminal Id
     *
     * @return string
     */
    public function getTerminalId();

    /**
     * Set terminal id
     *
     * @param string $terminalId
     * @return $this
     */
    public function setTerminalId($terminalId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \GhoSter\KbankPayments\Api\Data\TokenExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \GhoSter\KbankPayments\Api\Data\TokenExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \GhoSter\KbankPayments\Api\Data\TokenExtensionInterface $extensionAttributes = null
    );
}

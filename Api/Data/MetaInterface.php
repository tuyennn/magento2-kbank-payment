<?php

namespace GhoSter\KbankPayments\Api\Data;

/**
 * @api
 */
interface MetaInterface
{
    public const META_ID = 'meta_id';
    public const ORDER_ID = 'order_id';
    public const CUSTOMER_ID = 'customer_id';
    public const CHARGE_ID = 'charge_id';
    public const OBJECT = 'object';
    public const AMOUNT = 'amount';
    public const CURRENCY = 'currency';
    public const TRANSACTION_STATE = 'transaction_state';
    public const CREATED = 'created';
    public const STATUS = 'status';
    public const APPROVAL_CODE = 'approval_code';
    public const LIVEMODE = 'livemode';
    public const METADATA = 'metadata';
    public const FAILURE_CODE = 'failure_code';
    public const FAILURE_MESSAGE = 'failure_message';
    public const REDIRECT_URL = 'redirect_url';
    public const CARD_ID = 'card_id';
    public const CARD_BRAND = 'card_brand';
    public const ISSUER_BANK = 'issuer_bank';
    public const MASKED_PAN = 'masked_pan';
    public const REF1 = 'ref_1';
    public const REF2 = 'ref_2';
    public const REF3 = 'ref_3';
    public const BAHT_AMOUNT = 'baht_amount';
    public const RATE = 'rate';
    public const CONVERTED_AMOUNT = 'converted_amount';
    public const CONVERTED_CURRENCY_CODE = 'converted_currency_code';
    public const CONVERTED_CURRENCY_NAME = 'converted_currency_name';
    public const F63 = 'f63';
    public const MID = 'mid';

    /**
     * Get Meta Id
     *
     * @return int
     */
    public function getMetaId();

    /**
     * Set Meta Id
     *
     * @param int $metaId
     * @return $this
     */
    public function setMetaId(int $metaId);

    /**
     * Get Order Id
     *
     * @return string
     */
    public function getOrderId();

    /**
     * Set Order Id
     *
     * @param string $orderId
     * @return $this
     */
    public function setOrderId(string $orderId);

    /**
     * Get Customer Id
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get Charge Id
     *
     * @return string
     */
    public function getChargeId();

    /**
     * Set Charge Id
     *
     * @param string $chargeId
     * @return $this
     **/
    public function setChargeId($chargeId);

    /**
     * Get Object
     *
     * @return string
     */
    public function getObject();

    /**
     * Set Object
     *
     * @param string $object
     * @return $this
     */
    public function setObject($object);

    /**
     * Get Amount
     *
     * @return float
     */
    public function getAmount();

    /**
     * Set amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Get Currency
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Set currency
     *
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency);

    /**
     * Get Transaction State
     *
     * @return string
     */
    public function getTransactionState();

    /**
     * Set transaction state
     *
     * @param string $transactionState
     * @return $this
     */
    public function setTransactionState($transactionState);

    /**
     * Get Created
     *
     * @return string
     */
    public function getCreated();

    /**
     * Set created
     *
     * @param string $created
     * @return $this
     */
    public function setCreated($created);

    /**
     * Get Status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set Status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get Approval Code
     *
     * @return string
     */
    public function getApprovalCode();

    /**
     * Set Approval Code
     *
     * @param string $approvalCode
     * @return $this
     */
    public function setApprovalCode($approvalCode);

    /**
     * Get Live mode
     *
     * @return string
     */
    public function getLivemode();

    /**
     * Set Live mode
     *
     * @param string $livemode
     * @return $this
     */
    public function setLivemode($livemode);

    /**
     * Get Metadata
     *
     * @return string
     */
    public function getMetadata();

    /**
     * Set Metadata
     *
     * @param string $metadata
     * @return $this
     */
    public function setMetadata($metadata);

    /**
     * Get Failure Code
     *
     * @return string
     */
    public function getFailureCode();

    /**
     * Set Failure Code
     *
     * @param string $failureCode
     * @return $this
     */
    public function setFailureCode($failureCode);

    /**
     * Get Failure Message
     *
     * @return string
     */
    public function getFailureMessage();

    /**
     * Set Failure Message
     *
     * @param string $failureMessage
     * @return $this
     */
    public function setFailureMessage($failureMessage);

    /**
     * Get Redirect Url
     *
     * @return string
     */
    public function getRedirectUrl();

    /**
     * Set Redirect Url
     *
     * @param string $redirectUrl
     * @return $this
     */
    public function setRedirectUrl($redirectUrl);

    /**
     * Get Card Id
     *
     * @return string
     */
    public function getCardId();

    /**
     * Set card Id
     *
     * @param string $cardId
     * @return $this
     */
    public function setCardId($cardId);

    /**
     * Get Card Brand
     *
     * @return string
     */
    public function getCardBrand();

    /**
     * Set Card Brand
     *
     * @param string $cardBrand
     * @return $this
     */
    public function setCardBrand($cardBrand);

    /**
     * Get Issuer Bank
     *
     * @return string
     */
    public function getIssuerBank();

    /**
     * Set Issuer Bank
     *
     * @param string $issuerBank
     * @return $this
     */
    public function setIssuerBank($issuerBank);

    /**
     * Get Masked Pan
     *
     * @return string
     */
    public function getMaskedPan();

    /**
     * Set Masked Pan
     *
     * @param string $maskedPan
     * @return $this
     */
    public function setMaskedPan($maskedPan);

    /**
     * Get Ref 1
     *
     * @return string
     */
    public function getRef1();

    /**
     * Set Ref 1
     *
     * @param string $ref1
     * @return $this
     */
    public function setRef1($ref1);

    /**
     * Get Ref 2
     *
     * @return string
     */
    public function getRef2();

    /**
     * Set Ref 2
     *
     * @param string $ref2
     * @return $this
     */
    public function setRef2($ref2);

    /**
     * Get Ref 3
     *
     * @return string
     */
    public function getRef3();

    /**
     * Set Ref 3
     *
     * @param string $ref3
     * @return $this
     */
    public function setRef3($ref3);

    /**
     * Get Baht Amount
     *
     * @return string
     */
    public function getBahtAmount();

    /**
     * Set Baht Amount
     *
     * @param string $bahtAmount
     * @return $this
     */
    public function setBahtAmount($bahtAmount);

    /**
     * Get Rate
     *
     * @return string
     */
    public function getRate();

    /**
     * Set Rate
     *
     * @param string $rate
     * @return $this
     */
    public function setRate($rate);

    /**
     * Get Converted Amount
     *
     * @return string
     */
    public function getConvertedAmount();

    /**
     * Set Converted Amount
     *
     * @param string $convertedAmount
     * @return $this
     */
    public function setConvertedAmount($convertedAmount);

    /**
     * Get Converted Currency Code
     *
     * @return string
     */
    public function getConvertedCurrencyCode();

    /**
     * Set Converted Currency Code
     *
     * @param string $convertedCurrencyCode
     * @return $this
     */
    public function setConvertedCurrencyCode($convertedCurrencyCode);

    /**
     * Get Converted Currency Name
     *
     * @return string
     */
    public function getConvertedCurrencyName();

    /**
     * Set Converted Currency Name
     *
     * @param string $convertedCurrencyName
     * @return $this
     */
    public function setConvertedCurrencyName($convertedCurrencyName);

    /**
     * Get F63
     *
     * @return string
     */
    public function getF63();

    /**
     * Set F63
     *
     * @param string $f63
     * @return $this
     */
    public function setF63($f63);

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
}

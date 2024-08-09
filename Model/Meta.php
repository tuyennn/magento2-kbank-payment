<?php

namespace GhoSter\KbankPayments\Model;

use GhoSter\KbankPayments\Api\Data\MetaInterface;
use GhoSter\KbankPayments\Model\ResourceModel\Meta as ResourceMeta;
use Magento\Framework\Model\AbstractModel;

class Meta extends AbstractModel implements MetaInterface
{
    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init(ResourceMeta::class);
    }

    /**
     * @inheritdoc
     */
    public function getMetaId()
    {
        return $this->getData(self::META_ID);
    }

    /**
     * @inheritdoc
     */
    public function setMetaId(int $metaId)
    {
        return $this->setData(self::META_ID, $metaId);
    }

    /**
     * @inheritdoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOrderId(string $orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritdoc
     */
    public function getChargeId()
    {
        return $this->getData(self::CHARGE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setChargeId($chargeId)
    {
        return $this->setData(self::CHARGE_ID, $chargeId);
    }

    /**
     * @inheritdoc
     */
    public function getObject()
    {
        return $this->getData(self::OBJECT);
    }

    /**
     * @inheritdoc
     */
    public function setObject($object)
    {
        return $this->setData(self::OBJECT, $object);
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * @inheritdoc
     */
    public function getCurrency()
    {
        return $this->getData(self::CURRENCY);
    }

    /**
     * @inheritdoc
     */
    public function setCurrency($currency)
    {
        return $this->setData(self::CURRENCY, $currency);
    }

    /**
     * @inheritDoc
     */
    public function getTransactionState()
    {
        return $this->getData(self::TRANSACTION_STATE);
    }

    /**
     * @inheritDoc
     */
    public function setTransactionState($transactionState)
    {
        return $this->setData(self::TRANSACTION_STATE, $transactionState);
    }

    /**
     * @inheritDoc
     */
    public function getCreated()
    {
        return $this->getData(self::CREATED);
    }

    /**
     * @inheritDoc
     */
    public function setCreated($created)
    {
        return $this->setData(self::CREATED, $created);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getApprovalCode()
    {
        return $this->getData(self::APPROVAL_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setApprovalCode($approvalCode)
    {
        return $this->setData(self::APPROVAL_CODE, $approvalCode);
    }

    /**
     * @inheritDoc
     */
    public function getLivemode()
    {
        return $this->getData(self::LIVEMODE);
    }

    /**
     * @inheritDoc
     */
    public function setLivemode($livemode)
    {
        return $this->setData(self::LIVEMODE, $livemode);
    }

    /**
     * @inheritDoc
     */
    public function getMetadata()
    {
        return $this->getData(self::METADATA);
    }

    /**
     * @inheritDoc
     */
    public function setMetadata($metadata)
    {
        return $this->setData(self::METADATA, $metadata);
    }

    /**
     * @inheritDoc
     */
    public function getFailureCode()
    {
        return $this->getData(self::FAILURE_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setFailureCode($failureCode)
    {
        return $this->setData(self::FAILURE_CODE, $failureCode);
    }

    /**
     * @inheritDoc
     */
    public function getFailureMessage()
    {
        return $this->getData(self::FAILURE_MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setFailureMessage($failureMessage)
    {
        return $this->setData(self::FAILURE_MESSAGE, $failureMessage);
    }

    /**
     * @inheritDoc
     */
    public function getRedirectUrl()
    {
        return $this->getData(self::REDIRECT_URL);
    }

    /**
     * @inheritDoc
     */
    public function setRedirectUrl($redirectUrl)
    {
        return $this->setData(self::REDIRECT_URL, $redirectUrl);
    }

    /**
     * @inheritDoc
     */
    public function getCardId()
    {
        return $this->getData(self::CARD_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCardId($cardId)
    {
        return $this->setData(self::CARD_ID, $cardId);
    }

    /**
     * @inheritDoc
     */
    public function getCardBrand()
    {
        return $this->getData(self::CARD_BRAND);
    }

    /**
     * @inheritDoc
     */
    public function setCardBrand($cardBrand)
    {
        return $this->setData(self::CARD_BRAND, $cardBrand);
    }

    /**
     * @inheritDoc
     */
    public function getIssuerBank()
    {
        return $this->getData(self::ISSUER_BANK);
    }

    /**
     * @inheritDoc
     */
    public function setIssuerBank($issuerBank)
    {
        return $this->setData(self::ISSUER_BANK, $issuerBank);
    }

    /**
     * @inheritDoc
     */
    public function getMaskedPan()
    {
        return $this->getData(self::MASKED_PAN);
    }

    /**
     * @inheritDoc
     */
    public function setMaskedPan($maskedPan)
    {
        return $this->setData(self::MASKED_PAN, $maskedPan);
    }

    /**
     * @inheritDoc
     */
    public function getRef1()
    {
        return $this->getData(self::REF1);
    }

    /**
     * @inheritDoc
     */
    public function setRef1($ref1)
    {
        return $this->setData(self::REF1, $ref1);
    }

    /**
     * @inheritDoc
     */
    public function getRef2()
    {
        return $this->getData(self::REF2);
    }

    /**
     * @inheritDoc
     */
    public function setRef2($ref2)
    {
        return $this->setData(self::REF2, $ref2);
    }

    /**
     * @inheritDoc
     */
    public function getRef3()
    {
        return $this->getData(self::REF3);
    }

    /**
     * @inheritDoc
     */
    public function setRef3($ref3)
    {
        return $this->setData(self::REF3, $ref3);
    }

    /**
     * @inheritDoc
     */
    public function getBahtAmount()
    {
        return $this->getData(self::BAHT_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setBahtAmount($bahtAmount)
    {
        return $this->setData(self::BAHT_AMOUNT, $bahtAmount);
    }

    /**
     * @inheritDoc
     */
    public function getRate()
    {
        return $this->getData(self::RATE);
    }

    /**
     * @inheritDoc
     */
    public function setRate($rate)
    {
        return $this->setData(self::RATE, $rate);
    }

    /**
     * @inheritDoc
     */
    public function getConvertedAmount()
    {
        return $this->getData(self::CONVERTED_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setConvertedAmount($convertedAmount)
    {
        return $this->setData(self::CONVERTED_AMOUNT, $convertedAmount);
    }

    /**
     * @inheritDoc
     */
    public function getConvertedCurrencyCode()
    {
        return $this->getData(self::CONVERTED_CURRENCY_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setConvertedCurrencyCode($convertedCurrencyCode)
    {
        return $this->setData(self::CONVERTED_CURRENCY_CODE, $convertedCurrencyCode);
    }

    /**
     * @inheritDoc
     */
    public function getConvertedCurrencyName()
    {
        return $this->getData(self::CONVERTED_CURRENCY_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setConvertedCurrencyName($convertedCurrencyName)
    {
        return $this->setData(self::CONVERTED_CURRENCY_NAME, $convertedCurrencyName);
    }

    /**
     * @inheritDoc
     */
    public function getF63()
    {
        return $this->getData(self::F63);
    }

    /**
     * @inheritDoc
     */
    public function setF63($f63)
    {
        return $this->setData(self::F63, $f63);
    }

    /**
     * @inheritDoc
     */
    public function getMid()
    {
        return $this->getData(self::MID);
    }

    /**
     * @inheritDoc
     */
    public function setMid($mid)
    {
        return $this->setData(self::MID, $mid);
    }
}

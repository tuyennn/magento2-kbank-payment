<?php

namespace GhoSter\KbankPayments\Model\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use GhoSter\KbankPayments\Api\Data\NotifyInterface;

class Notify extends AbstractSimpleObject implements NotifyInterface
{

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getObject()
    {
        return $this->_get(self::OBJECT);
    }

    /**
     * @inheritDoc
     */
    public function setObject($object)
    {
        return $this->setData(self::OBJECT, $object);
    }

    /**
     * @inheritDoc
     */
    public function getAmount()
    {
        return $this->_get(self::AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * @inheritDoc
     */
    public function getCurrency()
    {
        return $this->_get(self::CURRENCY);
    }

    /**
     * @inheritDoc
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
        return $this->_get(self::TRANSACTION_STATE);
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
    public function getSource()
    {
        return $this->_get(self::SOURCE);
    }

    /**
     * @inheritDoc
     */
    public function setSource($source)
    {
        return $this->setData(self::SOURCE, $source);
    }

    /**
     * @inheritDoc
     */
    public function getCreated()
    {
        return $this->_get(self::CREATED);
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
        return $this->_get(self::STATUS);
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
    public function getLivemode()
    {
        return $this->_get(self::LIVEMODE);
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
        return $this->_get(self::METADATA);
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
        return $this->_get(self::FAILURE_CODE);
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
        return $this->_get(self::FAILURE_MESSAGE);
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
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function getMpi()
    {
        return $this->_get(self::MPI);
    }

    /**
     * @inheritDoc
     */
    public function setMpi($mpi)
    {
        return $this->setData(self::MPI, $mpi);
    }

    /**
     * @inheritDoc
     */
    public function getChecksum()
    {
        return $this->_get(self::CHECKSUM);
    }

    /**
     * @inheritDoc
     */
    public function setChecksum($checksum)
    {
        return $this->setData(self::CHECKSUM, $checksum);
    }

    /**
     * @inheritDoc
     */
    public function getRedirectUrl()
    {
        return $this->_get(self::REDIRECT_URL);
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
    public function getSettlementInfo()
    {
        return $this->_get(self::SETTLEMENT_INFO);
    }

    /**
     * @inheritDoc
     */
    public function setSettlementInfo($settlementInfo)
    {
        return $this->setData(self::SETTLEMENT_INFO, $settlementInfo);
    }

    /**
     * @inheritDoc
     */
    public function getRefundInfo()
    {
        return $this->_get(self::REFUND_INFO);
    }

    /**
     * @inheritDoc
     */
    public function setRefundInfo($refundInfo)
    {
        return $this->setData(self::REFUND_INFO, $refundInfo);
    }

    /**
     * @inheritDoc
     */
    public function getApprovalCode()
    {
        return $this->_get(self::APPROVAL_CODE);
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
    public function getCampaignId()
    {
        return $this->_get(self::CAMPAIGN_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCampaignId($campaignId)
    {
        return $this->setData(self::CAMPAIGN_ID, $campaignId);
    }

    /**
     * @inheritDoc
     */
    public function getRef1()
    {
        return $this->_get(self::REF_1);
    }

    /**
     * @inheritDoc
     */
    public function setRef1($ref1)
    {
        return $this->setData(self::REF_1, $ref1);
    }

    /**
     * @inheritDoc
     */
    public function getRef2()
    {
        return $this->_get(self::REF_2);
    }

    /**
     * @inheritDoc
     */
    public function setRef2($ref2)
    {
        return $this->setData(self::REF_2, $ref2);
    }

    /**
     * @inheritDoc
     */
    public function getRef3()
    {
        return $this->_get(self::REF_3);
    }

    /**
     * @inheritDoc
     */
    public function setRef3($ref3)
    {
        return $this->setData(self::REF_3, $ref3);
    }

    /**
     * @inheritDoc
     */
    public function getReferenceOrder()
    {
        return $this->_get(self::REFERENCE_ORDER);
    }

    /**
     * @inheritDoc
     */
    public function setReferenceOrder($referenceOrder)
    {
        return $this->setData(self::REFERENCE_ORDER, $referenceOrder);
    }
}

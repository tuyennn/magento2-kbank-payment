<?php

namespace GhoSter\KbankPayments\Api\Data;

/**
 * Interface NotifyInterface
 * @api
 */
interface NotifyInterface
{
    public const  ID = 'id';
    public const  OBJECT = 'object';
    public const  AMOUNT = 'amount';
    public const  CURRENCY = 'currency';
    public const  TRANSACTION_STATE = 'transaction_state';
    public const  SOURCE = 'source';
    public const  CREATED = 'created';
    public const  STATUS = 'status';
    public const  LIVEMODE = 'livemode';
    public const  METADATA = 'metadata';
    public const  FAILURE_CODE = 'failure_code';
    public const  FAILURE_MESSAGE = 'failure_message';
    public const  DESCRIPTION = 'description';
    public const  MPI = 'mpi';
    public const  CHECKSUM = 'checksum';
    public const  REDIRECT_URL = 'redirect_url';
    public const  SETTLEMENT_INFO = 'settlement_info';
    public const  REFUND_INFO = 'refund_info';
    public const  APPROVAL_CODE = 'approval_code';
    public const  CAMPAIGN_ID = 'campaign_id';
    public const  REF_1 = 'ref_1';
    public const  REF_2 = 'ref_2';
    public const  REF_3 = 'ref_3';
    public const  REFERENCE_ORDER = 'reference_order';

    /**
     * Get Id
     *
     * @return string
     */
    public function getId();

    /**
     * Set Id
     *
     * @param string $id
     * @return $this
     */
    public function setId($id);

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
     * Set Amount
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
     * Set Transaction State
     *
     * @param string $transactionState
     * @return $this
     */
    public function setTransactionState($transactionState);

    /**
     * Get Source
     *
     * @return \GhoSter\KbankPayments\Api\Data\SourceInterface
     */
    public function getSource();

    /**
     * Set Source
     *
     * @param \GhoSter\KbankPayments\Api\Data\SourceInterface $source
     * @return $this
     */
    public function setSource($source);

    /**
     * Get Created
     *
     * @return string
     */
    public function getCreated();

    /**
     * Set Created
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
     * Get live mode
     *
     * @return boolean
     */
    public function getLivemode();

    /**
     * Set live mode
     *
     * @param boolean $livemode
     * @return $this
     */
    public function setLivemode($livemode);

    /**
     * Get Metadata
     *
     * @return \Magento\Framework\DataObject
     */
    public function getMetadata();

    /**
     * Set Metadata
     *
     * @param \Magento\Framework\DataObject $metadata
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
     * Set failure code
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
     * Get Description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set Description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Get Mpi
     *
     * @return \GhoSter\KbankPayments\Api\Data\MpiInterface
     */
    public function getMpi();

    /**
     * Set Mpi
     *
     * @param \GhoSter\KbankPayments\Api\Data\MpiInterface $mpi
     * @return $this
     */
    public function setMpi($mpi);

    /**
     * Get Checksum
     *
     * @return string
     */
    public function getChecksum();

    /**
     * Set checksum
     *
     * @param string $checksum
     * @return $this
     */
    public function setChecksum($checksum);

    /**
     * Get redirect Url
     *
     * @return string
     */
    public function getRedirectUrl();

    /**
     * Set redirect url
     *
     * @param string $redirectUrl
     * @return $this
     */
    public function setRedirectUrl($redirectUrl);

    /**
     * Get Settlement Info
     *
     * @return string
     */
    public function getSettlementInfo();

    /**
     * Set Settlement Info
     *
     * @param string $settlementInfo
     * @return $this
     */
    public function setSettlementInfo($settlementInfo);

    /**
     * Get refund info
     *
     * @return string
     */
    public function getRefundInfo();

    /**
     * Set refund info
     *
     * @param string $refundInfo
     * @return $this
     */
    public function setRefundInfo($refundInfo);

    /**
     * Get Approval Code
     *
     * @return string
     */
    public function getApprovalCode();

    /**
     * Set approval code
     *
     * @param string $approvalCode
     * @return $this
     */
    public function setApprovalCode($approvalCode);

    /**
     * Get campaign id
     *
     * @return string
     */
    public function getCampaignId();

    /**
     * Set campaign id
     *
     * @param string $campaignId
     * @return $this
     */
    public function setCampaignId($campaignId);

    /**
     * Get Ref1
     *
     * @return string
     */
    public function getRef1();

    /**
     * Set Ref1
     *
     * @param string $ref1
     * @return $this
     */
    public function setRef1($ref1);

    /**
     * Get Ref2
     *
     * @return string
     */
    public function getRef2();

    /**
     * Set Ref2
     *
     * @param string $ref2
     * @return $this
     */
    public function setRef2($ref2);

    /**
     * Get Ref3
     *
     * @return string
     */
    public function getRef3();

    /**
     * Set Ref3
     *
     * @param string $ref3
     * @return $this
     */
    public function setRef3($ref3);

    /**
     * Get Reference Order
     *
     * @return string
     */
    public function getReferenceOrder();

    /**
     * Set Reference Order
     *
     * @param string $referenceOrder
     * @return $this
     */
    public function setReferenceOrder($referenceOrder);
}

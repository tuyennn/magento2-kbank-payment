<?php

namespace GhoSter\KbankPayments\Api\Data;

/**
 * Interface SourceInterface
 * @api
 */
interface SourceInterface
{
    public const  ID = 'id';
    public const  OBJECT = 'object';
    public const  BRAND = 'brand';
    public const  CARD_MASKING = 'card_masking';
    public const  ISSUER_BANK = 'issuer_bank';

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
     * Get Brand
     *
     * @return string
     */
    public function getBrand();

    /**
     * Set Brand
     *
     * @param string $brand
     * @return $this
     */
    public function setBrand($brand);

    /**
     * Get Card Masking
     *
     * @return string
     */
    public function getCardMasking();

    /**
     * Set Card Masking
     *
     * @param string $cardMasking
     * @return $this
     */
    public function setCardMasking($cardMasking);

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
}

<?php

namespace GhoSter\KbankPayments\Model\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use GhoSter\KbankPayments\Api\Data\SourceInterface;

class Source extends AbstractSimpleObject implements SourceInterface
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
    public function getBrand()
    {
        return $this->_get(self::BRAND);
    }

    /**
     * @inheritDoc
     */
    public function setBrand($brand)
    {
        return $this->setData(self::BRAND, $brand);
    }

    /**
     * @inheritDoc
     */
    public function getCardMasking()
    {
        return $this->_get(self::CARD_MASKING);
    }

    /**
     * @inheritDoc
     */
    public function setCardMasking($cardMasking)
    {
        return $this->setData(self::CARD_MASKING, $cardMasking);
    }

    /**
     * @inheritDoc
     */
    public function getIssuerBank()
    {
        return $this->_get(self::ISSUER_BANK);
    }

    /**
     * @inheritDoc
     */
    public function setIssuerBank($issuerBank)
    {
        return $this->setData(self::ISSUER_BANK, $issuerBank);
    }
}

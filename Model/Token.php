<?php

namespace GhoSter\KbankPayments\Model;

use GhoSter\KbankPayments\Api\Data\TokenInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class Token extends AbstractExtensibleModel implements TokenInterface
{
    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init(\GhoSter\KbankPayments\Model\ResourceModel\Token::class);
    }

    /**
     * @inheritDoc
     */
    public function getTokenId()
    {
        return $this->getData(self::TOKEN_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTokenId($tokenId)
    {
        return $this->setData(self::TOKEN_ID, $tokenId);
    }

    /**
     * @inheritDoc
     */
    public function getToken()
    {
        return $this->getData(self::TOKEN);
    }

    /**
     * @inheritDoc
     */
    public function setToken($token)
    {
        return $this->setData(self::TOKEN, $token);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentMethods()
    {
        return $this->getData(self::PAYMENT_METHODS);
    }

    /**
     * @inheritDoc
     */
    public function setPaymentMethods($paymentMethods)
    {
        return $this->setData(self::PAYMENT_METHODS, $paymentMethods);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedTime()
    {
        return $this->getData(self::CREATED_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedTime($createdTime)
    {
        return $this->setData(self::CREATED_TIME, $createdTime);
    }

    /**
     * @inheritDoc
     */
    public function getDccCurrency()
    {
        return $this->getData(self::DCC_CURRENCY);
    }

    /**
     * @inheritDoc
     */
    public function setDccCurrency($dccCurrency)
    {
        return $this->setData(self::DCC_CURRENCY, $dccCurrency);
    }

    /**
     * @inheritDoc
     */
    public function getSmartpayId()
    {
        return $this->getData(self::SMARTPAY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSmartpayId($smartpayId)
    {
        return $this->setData(self::SMARTPAY_ID, $smartpayId);
    }

    /**
     * @inheritDoc
     */
    public function getTerm()
    {
        return $this->getData(self::TERM);
    }

    /**
     * @inheritDoc
     */
    public function setTerm($term)
    {
        return $this->setData(self::TERM, $term);
    }

    /**
     * @inheritDoc
     */
    public function getSaveCard()
    {
        return $this->getData(self::SAVECARD);
    }

    /**
     * @inheritDoc
     */
    public function setSaveCard($saveCard)
    {
        return $this->setData(self::SAVECARD, $saveCard);
    }

    /**
     * @inheritDoc
     */
    public function getCardId()
    {
        return $this->getData(self::CARDID);
    }

    /**
     * @inheritDoc
     */
    public function setCardId($cardId)
    {
        return $this->setData(self::CARDID, $cardId);
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

    /**
     * @inheritDoc
     */
    public function getTerminalId()
    {
        return $this->getData(self::TERMINAL_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTerminalId($terminalId)
    {
        return $this->setData(self::TERMINAL_ID, $terminalId);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \GhoSter\KbankPayments\Api\Data\TokenExtensionInterface $extensionAttributes = null
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

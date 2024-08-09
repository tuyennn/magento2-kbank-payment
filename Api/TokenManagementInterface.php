<?php

namespace GhoSter\KbankPayments\Api;

use GhoSter\KbankPayments\Api\Data\MetaInterface;
use GhoSter\KbankPayments\Api\Data\TokenInterface;

interface TokenManagementInterface
{
    /**
     * Save new token for an order
     *
     * @param TokenInterface $token
     * @param string $orderIncrement
     * @return TokenInterface
     */
    public function saveTokenForOrder(TokenInterface $token, string $orderIncrement): TokenInterface;

    /**
     * Save new token then authorize for this order(creating charge)
     *
     * @param TokenInterface $token
     * @param string $orderIncrement
     * @return MetaInterface
     */
    public function saveTokenThenAuthorize(TokenInterface $token, string $orderIncrement): MetaInterface;
}

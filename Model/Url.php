<?php

namespace GhoSter\KbankPayments\Model;

use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;

class Url
{
    /**
     * @var Order|null
     */
    protected $order;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get Success Url
     *
     * @return string
     */
    public function getSuccessUrl(): string
    {
        return $this->getUrl('checkout/onepage/success');
    }

    /**
     * Get Failure Url
     *
     * @return string
     */
    public function getFailureUrl(): string
    {
        return $this->getUrl('checkout/onepage/failure');
    }

    /**
     * Get Url
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->urlBuilder->getUrl($route, $params);
    }

    /**
     * Set current order
     *
     * @param Order $order
     * @return $this
     */
    public function setOrder(Order $order): Url
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Get current order
     *
     * @return ?Order
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }
}

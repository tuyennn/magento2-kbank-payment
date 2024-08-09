<?php
namespace GhoSter\KbankPayments\Helper;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;

/**
 * Checkout Kbank Payments helper
 */
class Checkout
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param Session $session
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Session $session,
        UrlInterface $urlBuilder
    ) {
        $this->session = $session;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Cancel last placed order with specified comment message
     *
     * @param string $comment Comment appended to order history
     * @return bool True if order cancelled, false otherwise
     * @throws LocalizedException
     */
    public function cancelCurrentOrder($comment): bool
    {
        $order = $this->session->getLastRealOrder();
        if ($order->getId() && $order->getState() != Order::STATE_CANCELED) {
            $order->registerCancellation($comment)->save();
            return true;
        }
        return false;
    }
}

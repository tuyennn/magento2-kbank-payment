<?php

namespace GhoSter\KbankPayments\Controller\Token;

use GhoSter\KbankPayments\Controller\Kbank;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Sales\Model\Order;

/**
 * Class Process for processing the response token from gateway
 */
class Process extends Kbank implements CsrfAwareActionInterface, HttpPostActionInterface
{
    /**
     * @var Order|null
     */
    protected $order;

    /**
     * Process token
     *
     * @return Redirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $token = $this->getRequest()->getPostValue('token');
        $lastRealOrderId = $this->getRequest()->getParam('order_id');

        if (!$lastRealOrderId) {
            $lastRealOrderId = $this->checkoutSession->getLastRealOrderId();
        }

        if (!!$lastRealOrderId) {
            /** @var Order $order */
            $this->order = $this->orderFactory->create()->loadByIncrementId($lastRealOrderId);
        }

        $orderIncrementId = $this->order instanceof Order ? $this->order->getIncrementId() : 0;

        if (!empty($orderIncrementId)
            && !empty($token)
        ) {
            $this->url->setOrder($this->order);

            $storeId = $this->order->getStoreId();

            $result = $this->dataObjectFactory->create()
                ->addData(['result' => true, 'message' => '']);

            $meta = $this->metaDataFactory->create();

            $this->_eventManager->dispatch(
                'kbank_embedded_payment_method_process_token_data',
                [
                    'gateway_token' => $this->getRequest()->getPostValue(),
                    'order' => $this->order,
                    'result' => $result,
                    'meta' => $meta
                ]
            );

            if ($this->_checkOrderState($this->order)
                && !$this->kbankConfig->is3DS($storeId)
                && $result->getData('result') === true
            ) {
                /** Non 3DS */
                $resultRedirect->setUrl($this->url->getSuccessUrl());
            } elseif ($this->_checkOrderState($this->order)
                && $this->kbankConfig->is3DS($storeId)
                && $result->getData('result') === true
            ) {
                /** 3DS Support */
                try {
                    $meta = $this->metaRepository->getByOrderIncrement($this->order->getIncrementId());
                    if ($redirectUrl = $meta->getRedirectUrl()) {
                        $resultRedirect->setUrl($redirectUrl);
                        return $resultRedirect;
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Your payment has been declined. Please try again.'));
                    $resultRedirect->setUrl($this->url->getFailureUrl());
                }
            } else {
                $this->messageManager->addErrorMessage(__('Something went wrong while processing your order.'));
                $resultRedirect->setUrl($this->url->getFailureUrl());
            }
        }

        return $resultRedirect;
    }

    /**
     * Check order state
     *
     * @param Order $order
     * @return bool
     */
    protected function _checkOrderState(Order $order): bool
    {
        return in_array($order->getState(), $this->allowedOrderStates);
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

    /**
     * Disable Magento's CSRF validation.
     *
     * @param RequestInterface $request
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}

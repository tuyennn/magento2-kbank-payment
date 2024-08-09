<?php

namespace GhoSter\KbankPayments\Controller\Payment;

use GhoSter\KbankPayments\Api\Data\MetaInterface;
use GhoSter\KbankPayments\Controller\Kbank;
use GhoSter\KbankPayments\Gateway\Config;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Sales\Model\Order;

/**
 * Class Callback is used for 3DS payment support only
 */
class Callback extends Kbank implements CsrfAwareActionInterface
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var string|null
     */
    protected $chargeId;

    /**
     * @var string|null
     */
    protected $token;

    /**
     * @var Order|null
     */
    protected $order;

    /** @var MetaInterface|null */
    protected $meta;

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $this->_eventManager->dispatch(
            'none_samesite_response_before',
            ['request' => $this->getRequest(), 'header' => $this->httpHeader]
        );

        $validResponse = $this->_verifyResponse();

        if ($this->chargeId) {
            try {
                $this->meta = $this->metaRepository->getByChargeId($this->chargeId);

                $this->order = $this->orderFactory->create()
                    ->loadByIncrementId($this->meta->getOrderId());

                $this->url->setOrder($this->order);
            } catch (\Exception $e) {
                $this->errors[] = __('Meta was mismatched or empty.');
            }

            if (!$this->order) {
                $this->errors[] = __('Order was mismatched or empty.');
            }

            if ($validResponse && $this->order instanceof Order && $this->meta) {
                try {
                    if (!$this->_checkOrderState($this->order)) {
                        $this->logger
                            ->info(
                                sprintf(
                                    'Order status already changed. Do nothing: %s',
                                    $this->order->getStatus()
                                )
                            );
                    } else {
                        $errorObject = $this->dataObjectFactory
                            ->create(
                                [
                                    'data' => ['error' => $this->errors]
                                ]
                            );

                        $this->_eventManager->dispatch(
                            'kbank_embedded_payment_method_callback_validation',
                            [
                                'meta' => $this->meta,
                                'order' => $this->order,
                                'error' => $errorObject,
                                'type' => 'callback'
                            ]
                        );

                        $this->errors = $errorObject->getData('error');

                        if (!in_array($this->order->getStatus(), $this->processedOrderStates)) {
                            $this->errors[] = __('Callback Order was not processed correctly or order was cancelled');
                        }
                    }
                } catch (\Exception $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
        }

        if (!empty($this->errors)) {
            $comment = implode(',', $this->errors);
            $this->messageManager->addErrorMessage($comment);
            if ($this->order) {
                $this->order->cancel();
                $this->order->save();
            }
        }

        $resultRedirect->setUrl(empty($this->errors) ? $this->url->getSuccessUrl() : $this->url->getFailureUrl());
        return $resultRedirect;
    }

    /**
     * Verify the response
     *
     * @return bool
     */
    protected function _verifyResponse(): bool
    {
        $responseData = $this->getRequest()->getPostValue();

        if ($this->kbankConfig->isDebugMode()) {
            $this->logger->info('Header Callback response data from Callback Controller');
            $this->logger->info($this->getRequest()->getHeader('Content-Type'));
            $this->logger->info('Callback response data from Callback Controller');
            $this->logger->info($responseData);
        }

        $this->chargeId = $responseData['objectId'] ?? false;
        $this->token = $responseData['token'] ?? false;
        $status = $responseData['status'] ?? Config::CALLBACK_INVALID_STATUS;

        if ($status !== Config::CALLBACK_VALID_STATUS) {
            $this->logger->info(sprintf('Callback Controller: Status was mismatched or empty - %s', $status));
            $this->errors[] = __('Status was mismatched or empty.');
            return false;
        }

        if (!$this->chargeId) {
            $this->logger
                ->info(
                    sprintf('Callback Controller: Charge Id was mismatched or empty. - %s', $this->chargeId)
                );
            $this->errors[] = __('Charge Id was mismatched or empty.');
            return false;
        }

        if (!$this->token) {
            $this->logger->info(sprintf('Callback Controller: Token was mismatched or empty. - %s', $this->token));
            $this->errors[] = __('Token was mismatched or empty.');
            return false;
        }

        return true;
    }

    /**
     * Check order state
     *
     * @param Order $order
     * @return bool
     */
    protected function _checkOrderState($order): bool
    {
        if (!$order) {
            return false;
        }

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

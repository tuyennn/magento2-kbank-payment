<?php

namespace GhoSter\KbankPayments\Gateway\Http;

use InvalidArgumentException;
use GhoSter\KbankPayments\Gateway\Config;
use Magento\PaymentServicesBase\Model\ServiceClientInterface;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Psr\Log\LoggerInterface as Logger;

/**
 * A client that can communicate with the Kbank API
 */
class Client implements ClientInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ServiceClientInterface
     */
    private $httpClient;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $logData = [];

    /**
     * @param Logger $logger
     * @param ServiceClientInterface $httpClient
     * @param Config $config
     */
    public function __construct(
        Logger $logger,
        ServiceClientInterface $httpClient,
        Config $config
    ) {
        $this->httpClient = $httpClient;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     * @throws ClientException
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        try {
            $environment = $transferObject->getClientConfig() ? $transferObject->getClientConfig()['environment'] : '';

            $request = $transferObject->getBody();
            $request = array_merge($request, ['environment' => $environment]);

            return $this->postRequest($request);
            // phpcs:ignore Magento2.Exceptions.ThrowCatch
        } catch (\Exception $e) {
            $this->logger->critical($e);

            throw new ClientException(
                __('Something went wrong in the payment gateway.')
            );
        } finally {
            $this->logger->debug($this->logData);
        }
    }

    /**
     * Post request
     *
     * @param array $request
     * @return array
     * @throws \Exception
     */
    public function postRequest(array $request = []): array
    {
        $this->logData = [
            'request' => $request,
        ];
        $payLoadType = $request['payload_type'];
        $requestType = $request['method_type'];

        if (isset($request['payload_type'])) {
            unset($request['payload_type']);
        }

        if (isset($request['method_type'])) {
            unset($request['method_type']);
        }

        if (!in_array($requestType, ['GET', 'POST', 'PUT', 'DELETE'])) {
            //phpcs:ignore Magento2.Exceptions.DirectThrow
            throw new \Exception('Send first parameter must be "GET", "POST", "PUT" or "DELETE"');
        }

        try {

            $headers = [
                'Content-Type: application/json',
                'x-api-key: ' . $this->config->getSecretKey(),
            ];
            $response = $this->httpClient->request(
                $headers,
                $this->getEndpointUrl($payLoadType, $request),
                $requestType,
                empty($request) ? '' : json_encode($request),
                'json',
                $request['environment'] ?? ''
            );

            $this->logData['response'] = $response;

        } catch (InvalidArgumentException $e) {
            // phpcs:ignore Magento2.Exceptions.DirectThrow
            throw new ClientException(
                __('Invalid JSON was returned by the gateway.')
            );
        } finally {
            $this->logger->debug($this->logData);
        }

        return $response;
    }

    /**
     * Get Endpoint Url
     *
     * @param string $payloadType
     * @param array $request
     * @return string
     */
    private function getEndpointUrl(string $payloadType, array &$request = []): string
    {
        $url = $this->config->getApiUrl();

        switch ($payloadType) {
            case 'createAuthorizeRequest':
                $url .= '/card/v2/charge';
                break;

            case 'createSettleRequest':
                $url .= '/card/v2/charge/{charge_id}/settle';
                break;

            case 'transactionDetailsRequest':
                $url .= '/card/v2/charge/{charge_id}';
                break;

            case 'voidRequest':
                $url .= '/card/v2/charge/{charge_id}/void';
                break;

            case 'refundRequest':
                $url .= '/card/v2/charge/{charge_id}/refund';
                break;
            default:
                break;
        }

        if (preg_match_all('/{+(.*?)}/', $url, $matches)) {
            if (!empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    if (array_key_exists($match, $request)) {
                        $url = str_replace('{' . $match . '}', $request[$match], $url);
                        unset($request[$match]);
                    }
                }
            }
        }

        return $url;
    }
}

<?php

namespace GhoSter\KbankPayments\Gateway\Http;

use InvalidArgumentException;
use GhoSter\KbankPayments\Gateway\Config;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Serialize\Serializer\Json;
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
     * @var ZendClientFactory
     */
    private $httpClientFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var array
     */
    private $logData = [];

    /**
     * @param Logger $logger
     * @param ZendClientFactory $httpClientFactory
     * @param Config $config
     * @param Json $json
     */
    public function __construct(
        Logger $logger,
        ZendClientFactory $httpClientFactory,
        Config $config,
        Json $json
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->config = $config;
        $this->logger = $logger;
        $this->json = $json;
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
            $request = $transferObject->getBody();
            $this->logData = [
                'request' => $request,
            ];

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
     * @throws \Zend_Http_Client_Exception
     */
    public function postRequest(&$request)
    {
        $this->logData['request'] = $request;
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

        $url = $this->getEndpointUrl($payLoadType, $request);

        try {

            /** @var ZendClient $client */
            $client = $this->httpClientFactory->create();

            $client->setUri($url);
            $client->setConfig(['maxredirects' => 0, 'timeout' => 30]);
            $client->setHeaders([
                'Content-Type: application/json',
                'x-api-key: ' . $this->config->getSecretKey(),
            ]);

            $client->setMethod($requestType);

            if (!empty($request)) {
                $client->setRawData($this->json->serialize($request), 'application/json');
            }

            $responseBody = $client->request()
                ->getBody();

            $this->logData['response'] = $responseBody;

            $data = $this->json->unserialize($responseBody);
        } catch (InvalidArgumentException $e) {
            // phpcs:ignore Magento2.Exceptions.DirectThrow
            throw new \Exception('Invalid JSON was returned by the gateway');
        } finally {
            $this->logger->debug($this->logData);
        }

        return $data;
    }

    /**
     * Get Endpoint Url
     *
     * @param string $payloadType
     * @param array $request
     * @return string
     */
    private function getEndpointUrl($payloadType, &$request)
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

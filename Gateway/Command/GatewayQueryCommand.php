<?php

namespace GhoSter\KbankPayments\Gateway\Command;

use Exception;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface as Logger;
use Magento\Payment\Gateway\Command\Result\ArrayResult;

/**
 * Makes a request to the gateway and returns results
 */
class GatewayQueryCommand implements CommandInterface
{
    /**
     * @var BuilderInterface
     */
    private $requestBuilder;

    /**
     * @var TransferFactoryInterface
     */
    private $transferFactory;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param BuilderInterface $requestBuilder
     * @param TransferFactoryInterface $transferFactory
     * @param ClientInterface $client
     * @param Logger $logger
     * @param ValidatorInterface $validator
     */
    public function __construct(
        BuilderInterface $requestBuilder,
        TransferFactoryInterface $transferFactory,
        ClientInterface $client,
        Logger $logger,
        ValidatorInterface $validator
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->transferFactory = $transferFactory;
        $this->client = $client;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    public function execute(array $commandSubject)
    {
        $transferO = $this->transferFactory->create(
            $this->requestBuilder->build($commandSubject)
        );

        try {
            $response = $this->client->placeRequest($transferO);
        } catch (Exception $e) {
            $this->logger->critical($e);

            throw new CommandException(__('There was an error while trying to process the request.'));
        }

        $result = $this->validator->validate(
            array_merge($commandSubject, ['response' => $response])
        );
        if (!$result->isValid()) {
            throw new CommandException(__('There was an error while trying to process the request.'));
        }

        return new ArrayResult($response);
    }
}

<?php

namespace GhoSter\KbankPayments\Gateway\Command;

use GhoSter\KbankPayments\Gateway\Config;
use GhoSter\KbankPayments\Gateway\SubjectReader;
use Magento\Payment\Gateway\Command\CommandPool;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;

/**
 * Syncs the transaction status with kbank
 */
class FetchTransactionInfoCommand implements CommandInterface
{
    /**
     * @var CommandPool
     */
    private $commandPool;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var HandlerInterface|null
     */
    private $handler;

    /**
     * @param CommandPoolInterface $commandPool
     * @param SubjectReader $subjectReader
     * @param Config $config
     * @param HandlerInterface|null $handler
     */
    public function __construct(
        CommandPoolInterface $commandPool,
        SubjectReader $subjectReader,
        Config $config,
        HandlerInterface $handler = null
    ) {
        $this->commandPool = $commandPool;
        $this->subjectReader = $subjectReader;
        $this->config = $config;
        $this->handler = $handler;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $commandSubject)
    {
        $command = $this->commandPool->get('get_transaction_details');
        $result = $command->execute($commandSubject);
        $response = $result->get();

        if ($this->handler) {
            $this->handler->handle($commandSubject, $response);
        }

        $rawDetails = $source = [];
        foreach ($response as $key => &$value) {
            if (!$value || empty($value)) {
                $value = '';
            }

            if ($key == 'source') {
                $source = $value;
            }

            if (!is_array($value)) {
                $rawDetails[$key] = $response[$key];
            }
        }

        return array_merge($source, $rawDetails);
    }
}

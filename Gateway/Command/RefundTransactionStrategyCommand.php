<?php
namespace GhoSter\KbankPayments\Gateway\Command;

use Magento\Framework\Exception\NotFoundException;
use GhoSter\KbankPayments\Gateway\SubjectReader;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\CommandInterface;

/**
 * Chooses the best method of returning the payment based on the status of the transaction
 */
class RefundTransactionStrategyCommand implements CommandInterface
{
    private const REFUND = 'refund_settled';
    private const VOID = 'void';

    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @param CommandPoolInterface $commandPool
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        CommandPoolInterface $commandPool,
        SubjectReader $subjectReader
    ) {
        $this->commandPool = $commandPool;
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $commandSubject)
    {
        $command = $this->getCommand($commandSubject);

        $this->commandPool->get($command)
            ->execute($commandSubject);
    }

    /**
     * Determines the command that should be used based on the status of the transaction
     *
     * @param array $commandSubject
     * @return string
     * @throws CommandException|NotFoundException
     */
    private function getCommand(array $commandSubject)
    {
        $details = $this->commandPool->get('get_transaction_details')
            ->execute($commandSubject)
            ->get();

        if ($details['transaction_state'] === \GhoSter\KbankPayments\Gateway\Config::TRANSACTION_STATE_AUTH) {
            return self::VOID;
        } elseif ($details['transaction_state'] !== \GhoSter\KbankPayments\Gateway\Config::TRANSACTION_STATE_SETTLED) {
            throw new CommandException(__('This transaction cannot be refunded with its current status.'));
        }

        return self::REFUND;
    }
}

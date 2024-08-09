<?php
namespace GhoSter\KbankPayments\Gateway\Command;

use Magento\Framework\App\RequestInterface;
use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magento\Sales\Model\Order\Payment;
use Psr\Log\LoggerInterface;
use Magento\Framework\Phrase;

class CancelCommand implements CommandInterface
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * CancelCommand constructor.
     * @param RequestInterface $request
     * @param ValidatorInterface $validator
     * @param LoggerInterface $logger
     * @param HandlerInterface $handler
     */
    public function __construct(
        RequestInterface $request,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        HandlerInterface $handler
    ) {
        $this->request = $request;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->handler = $handler;
    }

    /**
     * Executes command basing on business object
     *
     * @param array $commandSubject
     * @return null|Command\ResultInterface
     * @throws CommandException
     */
    public function execute(array $commandSubject)
    {
        $response = $this->request->getParams();
        /** @var Payment $payment */
        $result = $this->validator->validate(
            array_merge($commandSubject, ['response' => $response])
        );

        if (!$result->isValid()) {
            $this->logExceptions($result->getFailsDescription());
            throw new CommandException(
                __('Transaction has been declined. Please try again later.')
            );
        }

        $this->handler->handle(
            array_merge($commandSubject),
            $response
        );
    }

    /**
     * Log exceptions
     *
     * @param Phrase[] $fails
     * @return void
     */
    private function logExceptions(array $fails)
    {
        foreach ($fails as $failPhrase) {
            $this->logger->info((string) $failPhrase);
        }
    }
}

<?php

namespace GhoSter\KbankPayments\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use GhoSter\KbankPayments\Gateway\SubjectReader;

/**
 * Validates the status of an attempted transaction
 *
 */
class TransactionResponseValidator extends AbstractValidator
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @param ResultInterfaceFactory $resultFactory
     * @param SubjectReader $subjectReader
     */
    public function __construct(ResultInterfaceFactory $resultFactory, SubjectReader $subjectReader)
    {
        parent::__construct($resultFactory);
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $response = $this->subjectReader->readResponse($validationSubject);

        if ($this->isResponseCodeAnError($response)) {
            $errorMessages = [];

            if (isset($response['failure_code'])) {
                $errorMessages[] = $response['failure_message'];
            }

            if ($response['object'] === 'error') {
                $errorMessages[] = $response['message'];
            }

            return $this->createResult(false, $errorMessages);
        }

        return $this->createResult(true);
    }

    /**
     * Determines if the response code is actually an error
     *
     * @param array $transactionResponse
     * @return bool
     */
    private function isResponseCodeAnError(array $transactionResponse)
    {
        $object = $transactionResponse['object'] ?? false;

        if (!$object) {
            return true;
        }

        if ($object == 'error') {
            return true;
        }

        if (isset($transactionResponse['failure_code']) || isset($transactionResponse['failure_message'])) {
            return true;

        }

        return false;
    }
}

<?php
namespace GhoSter\KbankPayments\Gateway\Validator;

use GhoSter\KbankPayments\Gateway\SubjectReader;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

/**
 * Validates that the request was successful
 *
 */
class GeneralResponseValidator extends AbstractValidator
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var ResultInterfaceFactory
     */
    private $resultFactory;

    /**
     * @param ResultInterfaceFactory $resultFactory
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        SubjectReader $subjectReader
    ) {
        parent::__construct($resultFactory);
        $this->resultFactory = $resultFactory;
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $response = $this->subjectReader->readResponse($validationSubject);
        $object = $response['object'];
        $isValid = ($object != 'error');
        $errorMessages = [];

        if (!$isValid) {
            if (isset($response['code'])) {
                $errorMessages[] = $response['message'];
            }
        }

        return $this->createResult($isValid, $errorMessages);
    }
}

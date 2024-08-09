<?php
namespace GhoSter\KbankPayments\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Adds the type of the request to the build subject
 */
class MethodTypeBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * Adds the type of the request to the build subject
     *
     * @param array $buildSubject
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build(array $buildSubject)
    {
        return [
            'method_type' => $this->type
        ];
    }
}
